package com.jasaferdi.fotovideograp.activities;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import com.jasaferdi.fotovideograp.C0476R;
import com.jasaferdi.fotovideograp.Model.Login.User;
import com.jasaferdi.fotovideograp.Model.Provider.MarkFavoriteParam;
import com.jasaferdi.fotovideograp.Model.Provider.ProviderModel;
import com.jasaferdi.fotovideograp.Retrofit.RetrofitUtil;
import com.jasaferdi.fotovideograp.R;
import com.jasaferdi.fotovideograp.Retrofit.RetrofitUtil;
import com.jasaferdi.fotovideograp.Utils.Constants;
import com.jasaferdi.fotovideograp.Utils.DatabaseUtil;
import com.jasaferdi.fotovideograp.Utils.SharedPreferenceUtil;
import com.jasaferdi.fotovideograp.adapters.ProviderListRecyclerViewAdapter;

import java.util.ArrayList;
import java.util.List;
import java.util.Collection;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

/**
 * A fragment representing a list of Items.
 * <p/>
 * interface.
 */
public class ProviderListActivity extends CommonProviderInfoActivity {

    private String category = Constants.EMPTY_STRING;
    private int categoryId = -1;
    private String keyword = Constants.EMPTY_STRING;
    private String location = Constants.EMPTY_STRING;
    private String country = Constants.EMPTY_STRING;
    private String city = Constants.EMPTY_STRING;
    private String zipCode = Constants.EMPTY_STRING;
    private String subCategory = "";
    private String isFeatured = Constants.EMPTY_STRING;
    private String radius = Constants.EMPTY_STRING;
    private String latitude = Constants.EMPTY_STRING;
    private String longitude = Constants.EMPTY_STRING;
    private int userId;
    private ProviderModel provider;

    @Override
    protected void setAdapter() {
        loadData();
    }

    protected void loadData() {
        if (getIntent() != null) {
            Bundle bundle = getIntent().getBundleExtra(Constants.DATA);
            if (bundle != null) {
                category = bundle.getString(Constants.CATEGORY);
                keyword = bundle.getString(Constants.KEYWORD);
                country = bundle.getString(Constants.COUNTRY);
                city = bundle.getString(Constants.CITY);
                location = bundle.getString(Constants.LOCATION);
                zipCode = bundle.getString(Constants.ZIP_CODE);
                radius = bundle.getString(Constants.DISTANCE);
                isFeatured = bundle.getString(Constants.IS_FEATURED);
                latitude = bundle.getString(Constants.LATITUDE);
                longitude = bundle.getString(Constants.LONGITUDE);
                this.categoryId = bundle.getInt(Constants.CATEGORY_ID);
            }
        }

        User user = DatabaseUtil.getInstance().getUser();
        if(SharedPreferenceUtil.getBoolen(this, Constants.ISUSERLOGGEDIN)
                && user != null) {
            userId = user.getData().getID();
        }

        if(isFeatured == null || isFeatured.isEmpty()) {
            RetrofitUtil.createProviderAPI().searchProvider(userId,
                    category,
                    keyword,
                    location,
                    radius,
                    country,
                    city,
                    zipCode,
                    latitude,
                    longitude).enqueue(dataCallBack);
        }else{
            RetrofitUtil.createProviderAPI().
                    getFeaturedProviders(userId,Constants.IS_FEATURED_CODE).enqueue(dataCallBack);
        }
        if (this.categoryId == -1) {
            RetrofitUtil.createProviderAPI().searchProvider(this.userId, this.keyword, this.location, this.radius, this.country, this.city, this.zipCode, this.latitude, this.longitude).enqueue(this.dataCallBack);
        } else {
            RetrofitUtil.createProviderAPI().searchProvider(this.userId, this.categoryId, this.keyword, this.location, this.radius, this.country, this.city, this.zipCode, this.latitude, this.longitude).enqueue(this.dataCallBack);
        }
    }

    @Override
    public void onProviderListInteraction(ProviderModel item) {
        Intent detailActiivtyIntent = new Intent(this, ProviderDetailActivity.class);
        detailActiivtyIntent.putExtra(Constants.DATA, item);
        provider = item;
        startActivityForResult(detailActiivtyIntent,1);
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);

        if (resultCode == Activity.RESULT_OK && data != null) {
            provider.setIsfav(((ProviderModel) data.getSerializableExtra(Constants.DATA)).isfav());
            getRecyclerView().getAdapter().notifyDataSetChanged();
        }
    }

    @Override
    public void onProviderFavorite(ProviderModel item) {
        if(SharedPreferenceUtil.getBoolen(ProviderListActivity.this, Constants.ISUSERLOGGEDIN)) {
            showProgressDialog(getString(R.string.msg_updating_fvrt));
            RetrofitUtil.createProviderAPI().updateUserFavorites(
                    new MarkFavoriteParam(item.getID(),
                            DatabaseUtil.getInstance().getUser().getData().getID(),
                            !item.isfav())).enqueue(RetrofitUtil.updateUserFavorites(item,this));
        }else{
            showDialogSignedUp(getString(R.string.err_login_to_fvrt),this);
        }
    }

    @Override
    public void onUpdateFavorites(ProviderModel item) {
        item.setIsfav(!item.isfav());
        if (getRecyclerView().getAdapter() != null) {
            getRecyclerView().getAdapter().notifyDataSetChanged();
        }
        hideProgressDialog();
    }

    @Override
    public void onPositiveClick(String msg) {
        super.onPositiveClick(msg);
        openActivityForResult(LoginActivity.class);
    }

    Callback<List<ProviderModel>> dataCallBack = new Callback<List<ProviderModel>>() {
        @Override
        public void onResponse(Call<List<ProviderModel>> call, Response<List<ProviderModel>> response) {
            if (response.isSuccessful()) {
                List<ProviderModel> data = new ArrayList<>();

                if (response.body() != null && !response.body().isEmpty()) {
                    data.addAll(response.body());
                    getRecyclerView().setAdapter(new ProviderListRecyclerViewAdapter(data, ProviderListActivity.this));
                } else {
                    showNoData();
                }
            } else {
                Log.d("QuestionsCallback", "Code: " + response.code() + " Message: " + response.message());
                showNoData();
            }
        }

        @Override
        public void onFailure(Call<List<ProviderModel>> call, Throwable t) {
            t.printStackTrace();
            showNoData();
        }
    };

}





