package com.jasaferdi.fotovideograp.fragments;


import android.content.Intent;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v7.widget.LinearLayoutManager;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.AutoCompleteTextView;
import android.widget.EditText;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.cooltechworks.views.shimmer.ShimmerRecyclerView;
import com.google.android.gms.common.ConnectionResult;
import com.google.android.gms.common.GooglePlayServicesNotAvailableException;
import com.google.android.gms.common.GooglePlayServicesRepairableException;
import com.google.android.gms.common.api.GoogleApiClient;
import com.google.android.gms.common.api.PendingResult;
import com.google.android.gms.common.api.ResultCallback;
import com.google.android.gms.location.places.Place;
import com.google.android.gms.location.places.PlaceBuffer;
import com.google.android.gms.location.places.Places;
import com.google.android.gms.location.places.ui.PlacePicker;
import com.google.android.gms.maps.model.LatLng;
import com.google.android.gms.maps.model.LatLngBounds;
import com.jasaferdi.fotovideograp.DataManager.CategoryDataManager;
import com.jasaferdi.fotovideograp.Model.Login.User;
import com.jasaferdi.fotovideograp.Model.Provider.ProviderModel;
import com.jasaferdi.fotovideograp.Model.categories.Category;
import com.jasaferdi.fotovideograp.R;
import com.jasaferdi.fotovideograp.Retrofit.RetrofitUtil;
import com.jasaferdi.fotovideograp.Utils.Constants;
import com.jasaferdi.fotovideograp.Utils.DatabaseUtil;
import com.jasaferdi.fotovideograp.Utils.SharedPreferenceUtil;
import com.jasaferdi.fotovideograp.Utils.UtilFirebaseAnalytics;
import com.jasaferdi.fotovideograp.activities.AdvanceSearch;
import com.jasaferdi.fotovideograp.activities.CategoryListActivity;
import com.jasaferdi.fotovideograp.activities.NavigationDrawerActivity;
import com.jasaferdi.fotovideograp.activities.ProviderListActivity;
import com.jasaferdi.fotovideograp.activities.SearchResultActivity;
import com.jasaferdi.fotovideograp.activities.SelectableItemActivity;
import com.jasaferdi.fotovideograp.adapters.CategoryRecyclerViewAdapter;
import com.jasaferdi.fotovideograp.adapters.FeaturedProviderListRecyclerViewAdapter;
import com.jasaferdi.fotovideograp.adapters.PlaceArrayAdapter;
import com.jasaferdi.fotovideograp.adapters.SingleItemRecyclerViewAdapter;

import java.util.ArrayList;
import java.util.List;

import static android.app.Activity.RESULT_OK;
import static com.jasaferdi.fotovideograp.Retrofit.RetrofitUtil.getProviders;
import static com.jasaferdi.fotovideograp.Retrofit.RetrofitUtil.loadCountries;
import static com.jasaferdi.fotovideograp.Utils.Constants.GOOGLE_API_CLIENT_ID;

/**
 * A simple {@link Fragment} subclass.
 * Use the {@link HomeFragment#newInstance} factory method to
 * create an instance of this fragment.
 */
public class HomeFragment extends BaseFragment implements
        GoogleApiClient.OnConnectionFailedListener,
        GoogleApiClient.ConnectionCallbacks {

    public static final int REQUEST_CODE_CATEGORY = 100;
    public static final int REQUEST_CODE_SUB_CATEGORY = 101;
    public static final int REQUEST_CODE_COUNTRIES = 102;
    public static final int REQUEST_CODE_CITY = 103;
    public static final int PLACE_PICKER_REQUEST = 104;
    public static final int REQUEST_UPDATE_FAVORITE = 105;
    public static final LatLngBounds BOUNDS_MOUNTAIN_VIEW = new LatLngBounds(
            new LatLng(37.398160, -122.180831), new LatLng(37.430610, -121.972090));
    private ShimmerRecyclerView categoryRecycler;
    private ShimmerRecyclerView featuredRecycler;
    private EditText keyword;
    private TextView category;
    private AutoCompleteTextView location;
    private RelativeLayout categoryAll;
    private RelativeLayout featuredAll;
    private int userId;
    private String place;
    private String latitude;
    private String longitude;

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
                latitude = queriedLocation.latitude + "";
                longitude = queriedLocation.longitude + "";
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

    /**
     * Use this factory method to create a new instance of
     * this fragment using the provided parameters.
     *
     * @return A new instance of fragment HomeFragment.
     */
    // TODO: Rename and change types and number of parameters
    public static HomeFragment newInstance() {
        HomeFragment fragment = new HomeFragment();
        return fragment;
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        View view = inflater.inflate(R.layout.activity_main, container, false);

        ((NavigationDrawerActivity) getActivity()).getSupportActionBar().setTitle(R.string.title_home);
        initViews(view);
        setListeners(view);
        User user = DatabaseUtil.getInstance().getUser();
        if (user != null && SharedPreferenceUtil.getBoolen(getActivity(), Constants.ISUSERLOGGEDIN)) {
            userId = user.getData().getID();
        }
        loadData();

        return view;
    }

    @Override
    public void onResume() {
        super.onResume();
        setLocationPicker();
    }

    @Override
    public void onPause() {
        super.onPause();
        if (mGoogleApiClient != null && mGoogleApiClient.isConnected()) {
            mGoogleApiClient.stopAutoManage(getActivity());
            mGoogleApiClient.disconnect();
        }
    }

    private void setLocationPicker() {
        if (mGoogleApiClient == null) {
            mGoogleApiClient = new GoogleApiClient.Builder(getActivity())
                    .addApi(Places.GEO_DATA_API)
                    .enableAutoManage(getActivity(), GOOGLE_API_CLIENT_ID, this)
                    .addConnectionCallbacks(this)
                    .build();
        } else {
            mGoogleApiClient.connect();
        }
        location.setOnItemClickListener(mAutocompleteClickListener);
        mPlaceArrayAdapter = new PlaceArrayAdapter(getActivity(), android.R.layout.simple_list_item_1,
                BOUNDS_MOUNTAIN_VIEW, null);
        location.setAdapter(mPlaceArrayAdapter);
    }

    private void loadData() {
        /*List<Category> categoriesList = DatabaseUtil.getInstance().getCategoriesList();
        if (categoriesList != null && !categoriesList.isEmpty()) {
            onCategoriesLoad(categoriesList);
        }else{*/
        new CategoryDataManager().loadDataFromServer(this, true);
        //}

        RetrofitUtil.createProviderAPI().getFeaturedProviders(userId, Constants.IS_FEATURED_CODE)
                .enqueue(getProviders(this));

        List<String> countries = DatabaseUtil.getInstance().getCountries();
        if (countries.isEmpty()) {
            RetrofitUtil.createProviderAPI().loadCountries().enqueue(loadCountries(this));
        }

    }

    private void initViews(View view) {
        keyword = view.findViewById(R.id.search_keyword);
        categoryAll = view.findViewById(R.id.categories_all);
        featuredAll = view.findViewById(R.id.featured_listing_all);
        category = view.findViewById(R.id.search_category);
        location = view.findViewById(R.id.search_location);
        categoryRecycler = view.findViewById(R.id.recyclerView_categories);
        featuredRecycler = view.findViewById(R.id.featured_listing);
        categoryRecycler.setLayoutManager(new LinearLayoutManager(getActivity()));
        featuredRecycler.setLayoutManager(new LinearLayoutManager(getActivity(), LinearLayoutManager.HORIZONTAL, false));
    }

    private void setListeners(View view) {
        categoryAll.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Intent detailActiivtyIntent = new Intent(getActivity(), CategoryListActivity.class);
                Bundle bundle = new Bundle();
                bundle.putString(Constants.TITLE, getString(R.string.all_categories));
                detailActiivtyIntent.putExtra(Constants.DATA, bundle);
                startActivity(detailActiivtyIntent);
            }
        });

        featuredAll.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Intent detailActiivtyIntent = new Intent(getActivity(), ProviderListActivity.class);
                Bundle bundle = new Bundle();
                bundle.putString(Constants.TITLE, getString(R.string.featured_provider));
                bundle.putString(Constants.IS_FEATURED, Constants.IS_FEATURED_CODE);
                detailActiivtyIntent.putExtra(Constants.DATA, bundle);
                startActivity(detailActiivtyIntent);
            }
        });
        view.findViewById(R.id.btnSearch).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                String key = keyword.getText() != null ? keyword.getText().toString() : Constants.EMPTY_STRING;
                Intent newIntent = new Intent(getActivity(), SearchResultActivity.class);

                Bundle bundle = new Bundle();
                bundle.putString(Constants.KEYWORD, key);
                bundle.putString(Constants.CATEGORY, category.getText().toString());

                if (location.getText() != null && !location.getText().toString().isEmpty()) {
                    bundle.putString(Constants.LOCATION, place);
                    bundle.putString(Constants.LATITUDE, latitude);
                    bundle.putString(Constants.LONGITUDE, longitude);
                }

                newIntent.putExtra(Constants.DATA, bundle);
                startActivity(newIntent);
                UtilFirebaseAnalytics.logEvent(Constants.EVENT_SEARCH, bundle);
            }
        });
        view.findViewById(R.id.home_advance_search).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Intent newIntent = new Intent(getActivity(), AdvanceSearch.class);
                startActivity(newIntent);
                UtilFirebaseAnalytics.logEvent(Constants.EVENT_CLICK, "Open", Constants.EVENT_ADVANCE_SEARCH);
            }
        });

        view.findViewById(R.id.search_category_view).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Intent newIntent = new Intent(getActivity(), SelectableItemActivity.class);
                ArrayList<String> categories = DatabaseUtil.getInstance().getCategories();
                newIntent.putStringArrayListExtra(Constants.DATA, categories);
                newIntent.putExtra(Constants.TITLE, getString(R.string.title_category));
                if(category.getText() != null) {
                    newIntent.putExtra(Constants.SELECTED_ITEM, category.getText().toString());
                }
                startActivityForResult(newIntent, REQUEST_CODE_CATEGORY);
            }
        });

        view.findViewById(R.id.current_location).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {

                PlacePicker.IntentBuilder builder = new PlacePicker.IntentBuilder();

                try {
                    startActivityForResult(builder.build(getActivity()), PLACE_PICKER_REQUEST);
                } catch (GooglePlayServicesRepairableException e) {
                    e.printStackTrace();
                } catch (GooglePlayServicesNotAvailableException e) {
                    e.printStackTrace();
                }
            }
        });
    }

    @Override
    public void onActivityResult(int requestCode, int resultCode, Intent data) {
        if (requestCode == REQUEST_CODE_CATEGORY) {
            if (resultCode == RESULT_OK) {
                List categoryList = data.getStringArrayListExtra(Constants.DATA);
                if (categoryList != null && !categoryList.isEmpty()) {
                    category.setText(categoryList.get(0).toString());
                }else{
                    category.setText(Constants.EMPTY_STRING);
                }
            }
        }
        if (requestCode == PLACE_PICKER_REQUEST) {
            if (resultCode == RESULT_OK) {
                if (getActivity() != null) {
                    Place plac = PlacePicker.getPlace(getActivity(), data);
                    String toastMsg = plac.getAddress().toString();
                    location.setText(toastMsg);
                    place = toastMsg;
                    latitude = plac.getLatLng().latitude + "";
                    longitude = plac.getLatLng().longitude + "";
                }
            }
        }
        super.onActivityResult(requestCode, resultCode, data);
    }

    @Override
    public void notifyChange() {
        if (featuredRecycler.getAdapter() != null) {
            featuredRecycler.getAdapter().notifyDataSetChanged();
        }
    }

    @Override
    public void onCategoriesLoad(List<Category> items) {
        if (items != null && items.size() > 3) {
            categoryRecycler.setAdapter(new CategoryRecyclerViewAdapter(items.subList(0, 3), HomeFragment.this));
        } else {
            categoryRecycler.setAdapter(new CategoryRecyclerViewAdapter(items, HomeFragment.this));
        }
    }

    @Override
    public void onProviderLoad(List<ProviderModel> items) {
        featuredRecycler.setAdapter(new FeaturedProviderListRecyclerViewAdapter(items, HomeFragment.this));
    }

    public void onError(Constants.Errors errorCode, String error) {
        try {
            List<String> list = new ArrayList<String>() {{
                add(getString(R.string.err_something_wrong));
            }};
            switch (errorCode) {
                case CATEGORY_FAILED:
                    setCategoryOnError(list);
                    break;
                case PROVIDER:
                    featuredRecycler.setAdapter(null);
                    break;

            }
        } catch (IllegalStateException e) {
            e.printStackTrace();
        }
    }

    private void setCategoryOnError(List<String> list) {
        List<Category> categoriesList = DatabaseUtil.getInstance().getCategoriesList();
        if (categoriesList != null && !categoriesList.isEmpty()) {
            onCategoriesLoad(categoriesList);
        } else {
            categoryRecycler.setAdapter(new SingleItemRecyclerViewAdapter(list));
        }
    }

    @Override
    public void onConnected(Bundle bundle) {
        mPlaceArrayAdapter.setGoogleApiClient(mGoogleApiClient);
    }

    @Override
    public void onConnectionFailed(ConnectionResult connectionResult) {
        Toast.makeText(getActivity(),
                getString(R.string.err_something_wrong),
                Toast.LENGTH_LONG).show();
    }


    @Override
    public void onConnectionSuspended(int i) {
        mPlaceArrayAdapter.setGoogleApiClient(null);
    }

}
