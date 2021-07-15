package com.jasaferdi.fotovideograp.activities;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.AdapterView;
import android.widget.AutoCompleteTextView;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.google.android.gms.common.ConnectionResult;
import com.google.android.gms.common.api.GoogleApiClient;
import com.google.android.gms.common.api.PendingResult;
import com.google.android.gms.common.api.ResultCallback;
import com.google.android.gms.location.places.Place;
import com.google.android.gms.location.places.PlaceBuffer;
import com.google.android.gms.location.places.Places;
import com.google.android.gms.maps.model.LatLng;
import com.jasaferdi.fotovideograp.DataManager.CategoryDataManager;
import com.jasaferdi.fotovideograp.R;
import com.jasaferdi.fotovideograp.Utils.AppUtils;
import com.jasaferdi.fotovideograp.Utils.Constants;
import com.jasaferdi.fotovideograp.Utils.DatabaseUtil;
import com.jasaferdi.fotovideograp.Utils.UtilFirebaseAnalytics;
import com.jasaferdi.fotovideograp.adapters.PlaceArrayAdapter;
import com.jasaferdi.fotovideograp.fragments.HomeFragment;

import java.util.List;

import static com.jasaferdi.fotovideograp.Utils.Constants.EMPTY_STRING;
import static com.jasaferdi.fotovideograp.Utils.Constants.GOOGLE_API_CLIENT_ID;
import static com.jasaferdi.fotovideograp.fragments.HomeFragment.REQUEST_CODE_CATEGORY;
import static com.jasaferdi.fotovideograp.fragments.HomeFragment.REQUEST_CODE_CITY;
import static com.jasaferdi.fotovideograp.fragments.HomeFragment.REQUEST_CODE_COUNTRIES;
import static com.jasaferdi.fotovideograp.fragments.HomeFragment.REQUEST_CODE_SUB_CATEGORY;

public class AdvanceSearch extends BaseActivity implements
        GoogleApiClient.OnConnectionFailedListener,
        GoogleApiClient.ConnectionCallbacks{

    private EditText keyword;
    private AutoCompleteTextView location;
    private EditText zipCode;
    private TextView category;
    private TextView subCategory;
    private TextView country;
    private TextView city;
    private Button search;
    private TextView radiusSearch;

    private LinearLayout countryView;
    private LinearLayout cityView;
    private LinearLayout categoryView;
    private LinearLayout subCategoryView;
    private String place;
    private String latitude;
    private String longitude;
    private ImageView currentLocation;

    private GoogleApiClient mGoogleApiClient;
    private PlaceArrayAdapter mPlaceArrayAdapter;

    private ResultCallback<PlaceBuffer> mUpdatePlaceDetailsCallback
            = new ResultCallback<PlaceBuffer>() {
        @Override
        public void onResult(PlaceBuffer places) {
            if (places.getStatus().isSuccess()) {
                final Place myPlace = places.get(0);
                LatLng queriedLocation = myPlace.getLatLng();
                place = myPlace.getAddress().toString();
                latitude = queriedLocation.latitude +"";
                longitude = queriedLocation.longitude +"";
            }
            places.release();
        }
    };
    private AdapterView.OnItemClickListener mAutocompleteClickListener
            = new AdapterView.OnItemClickListener() {
        @Override
        public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
            final PlaceArrayAdapter.PlaceAutocomplete item = mPlaceArrayAdapter.getItem(position);
            final String placeId = String.valueOf(item.placeId);
            PendingResult<PlaceBuffer> placeResult = Places.GeoDataApi
                    .getPlaceById(mGoogleApiClient, placeId);
            placeResult.setResultCallback(mUpdatePlaceDetailsCallback);
        }
    };

    @Override
    public void onResume() {
        super.onResume();
        setLocationPicker();
    }

    @Override
    public void onPause() {
        super.onPause();
        if (mGoogleApiClient != null && mGoogleApiClient.isConnected()) {
            mGoogleApiClient.stopAutoManage(this);
            mGoogleApiClient.disconnect();
        }
    }

    private void setLocationPicker() {
        if(mGoogleApiClient == null) {
            mGoogleApiClient = new GoogleApiClient.Builder(this)
                    .addApi(Places.GEO_DATA_API)
                    .enableAutoManage(this, GOOGLE_API_CLIENT_ID, this)
                    .addConnectionCallbacks(this)
                    .build();
        }else{
            mGoogleApiClient.connect();
        }
        location.setOnItemClickListener(mAutocompleteClickListener);
        mPlaceArrayAdapter = new PlaceArrayAdapter(this, android.R.layout.simple_list_item_1,
                HomeFragment.BOUNDS_MOUNTAIN_VIEW, null);
        location.setAdapter(mPlaceArrayAdapter);
    }

    @Override
    public void onConnected(Bundle bundle) {
        mPlaceArrayAdapter.setGoogleApiClient(mGoogleApiClient);
    }

    @Override
    public void onConnectionFailed(ConnectionResult connectionResult) {
        Toast.makeText(this,
                getString(R.string.err_something_wrong),
                Toast.LENGTH_LONG).show();
    }


    @Override
    public void onConnectionSuspended(int i) {
        mPlaceArrayAdapter.setGoogleApiClient(null);
    }

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_advance_search);
        initViews();
        addListener();
        getBundleData();
        getSupportActionBar().setTitle(R.string.advance_search);
        new CategoryDataManager().loadDataFromServer(this,false);
    }

    private void initViews() {
        keyword = findViewById(R.id.search_keyword);
        location = findViewById(R.id.search_location);
        country = findViewById(R.id.search_country);
        city = findViewById(R.id.search_city);
        zipCode = findViewById(R.id.search_zipcode);
        category = findViewById(R.id.search_category);
        subCategory = findViewById(R.id.search_sub_category);
        search = findViewById(R.id.search);
        radiusSearch = findViewById(R.id.search_radius);

        countryView = findViewById(R.id.adv_search_country);
        cityView = findViewById(R.id.adv_search_city);
        categoryView = findViewById(R.id.adv_search_category);
        subCategoryView = findViewById(R.id.adv_search_sub_category);

        currentLocation = findViewById(R.id.current_location);
    }

    private void getBundleData(){
        if(getIntent() != null) {
            Bundle bundle = getIntent().getBundleExtra(Constants.DATA);
            if(bundle != null) {
                category.setText(bundle.getString(Constants.CATEGORY));
                keyword.setText(bundle.getString(Constants.KEYWORD));
                location.setText(bundle.getString(Constants.LOCATION));
                country.setText(bundle.getString(Constants.COUNTRY));
                city.setText(bundle.getString(Constants.CITY));
                zipCode.setText(bundle.getString(Constants.ZIP_CODE));
                radiusSearch.setText(bundle.getString(Constants.DISTANCE));
            }
        }
    }

    private void addListener() {
        search.setOnClickListener(this);
        category.setOnClickListener(this);
        subCategory.setOnClickListener(this);
        country.setOnClickListener(this);
        city.setOnClickListener(this);

        countryView.setOnClickListener(this);
        subCategoryView.setOnClickListener(this);
        categoryView.setOnClickListener(this);
        cityView.setOnClickListener(this);
        currentLocation.setOnClickListener(this);
        radiusSearch.setOnClickListener(this);
    }

    @Override
    public void onClick(View view) {
        int id = view.getId();

        switch (id){
            case R.id.search:
                Bundle data = getData();
                openAcitivty(data,SearchResultActivity.class);
                UtilFirebaseAnalytics.logEvent(Constants.EVENT_ADVANCE_SEARCH,data);
                break;
            case R.id.search_category:
            case R.id.adv_search_category:
                seletecCategory();
                break;
            case R.id.search_sub_category:
            case R.id.adv_search_sub_category:
                selectSubCategory();
                break;
            case R.id.search_country:
            case R.id.adv_search_country:
                selectCountry();
                break;
            case R.id.search_city:
            case R.id.adv_search_city:
                selectCity();
                break;
            case R.id.current_location:
                pickLocation();
                break;
            case R.id.search_radius:
                showRadiusDialog(this,radiusSearch.getText().toString());
                break;

        }

    }

    private void selectCountry() {
        String selectedCountry = EMPTY_STRING;
        if(city.getText() != null) {
            selectedCountry = subCategory.getText().toString();
        }
        openAcitivty(DatabaseUtil.getInstance().getCountries()
                ,SelectableItemActivity.class,REQUEST_CODE_COUNTRIES,getString(R.string.title_country), selectedCountry);
    }

    private void seletecCategory() {
        String cat = EMPTY_STRING;
        if(category.getText() != null) {
            cat = category.getText().toString();
        }
        openAcitivty(DatabaseUtil.getInstance().getCategories(),
                SelectableItemActivity.class,REQUEST_CODE_CATEGORY,getString(R.string.title_category), cat);
    }

    @Override
    public void onPositiveClick(String msg) {
        super.onPositiveClick(msg);
        radiusSearch.setText(msg);
    }

    protected void setLocation(String name, Place plac){
        location.setText(name);
        place = name;
        latitude = plac.getLatLng().latitude +"";
        longitude = plac.getLatLng().longitude +"";
    }

    private void selectCity() {
        if(!country.getText().toString().isEmpty()) {
            String selectedCity = EMPTY_STRING;
            if(city.getText() != null) {
                selectedCity = city.getText().toString();
            }
            openAcitivty(DatabaseUtil.getInstance().getCities(country.getText().toString())
                    , SelectableItemActivity.class, REQUEST_CODE_CITY,getString(R.string.title_city), selectedCity);
        }else{
            AppUtils.showDialog(this,getString(R.string.msg_select_country),null);
        }
    }

    private void selectSubCategory() {
        if(!category.getText().toString().isEmpty()) {
            String selectedSubCat = EMPTY_STRING;
            if(subCategory.getText() != null) {
                selectedSubCat = subCategory.getText().toString();
            }
            openAcitivty(DatabaseUtil.getInstance().getSubCategories(category.getText().toString())
                    , SelectableItemActivity.class, REQUEST_CODE_SUB_CATEGORY,getString(R.string.title_sub_category), selectedSubCat);
        }else{
            AppUtils.showDialog(this,getString(R.string.msg_select_category),null);
        }
    }

    @Override
    public void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);

        if(resultCode == RESULT_OK){
                if (requestCode == REQUEST_CODE_CATEGORY) {

                    List categoryList = data.getStringArrayListExtra(Constants.DATA);
                    if(categoryList != null && !categoryList.isEmpty()) {
                        String name = categoryList.get(0).toString();
                        if (category.getText() != null && !category.getText().toString().equals(name)) {
                            subCategory.setText(Constants.EMPTY_STRING);
                        }
                        if (!categoryList.isEmpty()) {
                            category.setText(name);
                        }
                    }else{
                        category.setText(EMPTY_STRING);
                        subCategory.setText(EMPTY_STRING);
                    }
                }

                if (requestCode == REQUEST_CODE_SUB_CATEGORY) {
                    List categoryList = data.getStringArrayListExtra(Constants.DATA);
                    if (categoryList != null && !categoryList.isEmpty()) {
                        subCategory.setText(categoryList.get(0).toString());
                    }else{
                        subCategory.setText(EMPTY_STRING);
                    }
                }

                if (requestCode == REQUEST_CODE_COUNTRIES) {
                    List categoryList = data.getStringArrayListExtra(Constants.DATA);
                    if(categoryList != null && !categoryList.isEmpty()) {
                        String name = categoryList.get(0).toString();
                        if (country.getText() != null && !country.getText().toString().equals(name)) {
                            city.setText(Constants.EMPTY_STRING);
                        }
                        if (!categoryList.isEmpty()) {
                            country.setText(name);
                        }
                    }else{
                        country.setText(EMPTY_STRING);
                        city.setText(EMPTY_STRING);
                    }
                }

                if (requestCode == REQUEST_CODE_CITY) {
                    List categoryList = data.getStringArrayListExtra(Constants.DATA);
                    if (categoryList != null && !categoryList.isEmpty()) {
                        city.setText(categoryList.get(0).toString());
                    }else{
                        city.setText(EMPTY_STRING);
                    }
                }
            }
    }


    private Bundle getData(){
        Bundle bundle = new Bundle();

        if(keyword.getText() != null)
        bundle.putString(Constants.KEYWORD,keyword.getText().toString());

        if(location.getText() != null && !location.getText().toString().isEmpty()) {
            bundle.putString(Constants.LOCATION, place);
            bundle.putString(Constants.LATITUDE, latitude);
            bundle.putString(Constants.LONGITUDE, longitude);
        }

        if(country.getText() != null)
            bundle.putString(Constants.COUNTRY,country.getText().toString());

        if(city.getText() != null)
            bundle.putString(Constants.CITY,city.getText().toString());

        if(zipCode.getText() != null)
            bundle.putString(Constants.ZIP_CODE,zipCode.getText().toString());

        if(category.getText() != null)
            bundle.putString(Constants.CATEGORY,category.getText().toString());

        if(subCategory.getText() != null)
            bundle.putString(Constants.SUB_CATEGORY,subCategory.getText().toString());

        if(radiusSearch.getText() != null)
            bundle.putString(Constants.DISTANCE,radiusSearch.getText().toString());

        return bundle;
    }

}
