package com.jasaferdi.fotovideograp.fragments;


import android.app.ProgressDialog;
import android.content.Intent;
import android.os.Bundle;
import android.support.annotation.NonNull;
import android.support.annotation.Nullable;
import android.support.v4.app.Fragment;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;

import com.jasaferdi.fotovideograp.DataManager.CategoryDataManager;
import com.jasaferdi.fotovideograp.Interface.OnDataLoadListener;
import com.jasaferdi.fotovideograp.Interface.OnListInteractionListener;
import com.jasaferdi.fotovideograp.Model.Appointment;
import com.jasaferdi.fotovideograp.Model.Countries;
import com.jasaferdi.fotovideograp.Model.JobItem;
import com.jasaferdi.fotovideograp.Model.Provider.BusinessHours;
import com.jasaferdi.fotovideograp.Model.Provider.PrivacySettings;
import com.jasaferdi.fotovideograp.Model.Provider.ProfileServices;
import com.jasaferdi.fotovideograp.Model.Provider.ProviderModel;
import com.jasaferdi.fotovideograp.Model.Provider.ProviderReviewListData;
import com.jasaferdi.fotovideograp.Model.categories.Category;
import com.jasaferdi.fotovideograp.Utils.AppUtils;
import com.jasaferdi.fotovideograp.Utils.Constants;
import com.jasaferdi.fotovideograp.activities.ProviderDetailActivity;
import com.jasaferdi.fotovideograp.activities.ProviderListActivity;

import java.util.List;

import static android.app.Activity.RESULT_OK;
import static com.jasaferdi.fotovideograp.fragments.HomeFragment.REQUEST_UPDATE_FAVORITE;

/**
 * A simple {@link Fragment} subclass.
 */
public class BaseFragment extends Fragment implements OnListInteractionListener,OnDataLoadListener{

    private ProgressDialog progressDialog;
    protected ProviderModel provider;
    @Nullable
    @Override
    public View onCreateView(@NonNull LayoutInflater inflater, @Nullable ViewGroup container,
                             @Nullable Bundle savedInstanceState) {

        progressDialog = new ProgressDialog(getActivity());
        progressDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);

        if(getActivity() != null) {
            getActivity().overridePendingTransition(android.R.anim.fade_in, android.R.anim.fade_out);
        }
        return super.onCreateView(inflater, container, savedInstanceState);
    }

    @Override
    public void onProviderListInteraction(ProviderModel item) {
        Intent detailActiivtyIntent = new Intent(getActivity(), ProviderDetailActivity.class);
        detailActiivtyIntent.putExtra(Constants.DATA, item);
        provider = item;
        startActivityForResult(detailActiivtyIntent,REQUEST_UPDATE_FAVORITE);
    }

    @Override
    public void onCategoryListInteraction(Category item) {
        try {
            Intent newIntent = new Intent(getActivity(), ProviderListActivity.class);
            Bundle bundle = new Bundle();
            bundle.putString(Constants.CATEGORY, item.getTitle());
            bundle.putString(Constants.TITLE, item.getTitle());
            newIntent.putExtra(Constants.DATA, bundle);
            newIntent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_NEW_TASK);
            startActivity(newIntent);
        }catch (Exception e){
            e.printStackTrace();
            new CategoryDataManager().loadDataFromServer(this,true);
        }
    }

    @Override
    public void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);

        if (requestCode == REQUEST_UPDATE_FAVORITE && resultCode == RESULT_OK
                && data != null &&  provider != null) {
            provider.setIsfav(((ProviderModel) data.getSerializableExtra(Constants.DATA)).isfav());
            notifyChange();
        }
    }

    protected void notifyChange(){

    }

    @Override
    public void onAppointmentInteraction(Appointment item, int pos) {

    }

    @Override
    public void onProviderFavorite(ProviderModel item) {

    }

    @Override
    public void onJobItemSelection(JobItem item) {

    }

    @Override
    public void onUserMessageSelection(String path, int post) {

    }

    @Override
    public void removeItem(int position) {

    }

    @Override
    public void onCategoriesLoad(List<Category> items) {
    }

    @Override
    public void onProviderLoad(List<ProviderModel> items) {
    }

    @Override
    public void onCountriesLoad(List<Countries> items) {

    }

    @Override
    public void onAppointmentsLoad(List<Appointment> items) {

    }

    @Override
    public void onServiceLoad(List<ProfileServices> items) {

    }

    @Override
    public void onReviewsLoad(List<ProviderReviewListData> items) {

    }

    @Override
    public void onUpdateFavorites(ProviderModel item) {

    }

    @Override
    public void onSuccess(String msg) {
        hideProgressDialog();
        AppUtils.showDialog(getActivity(),msg,null);
    }

    @Override
    public void onError(Constants.Errors errorCode, String error) {
        hideProgressDialog();
        AppUtils.showDialog(getActivity(),error,null);

    }

    @Override
    public void onBusinessHoursLoaded(BusinessHours item) {

    }

    protected  void setAdapter(){

    }

    public RecyclerView getRecyclerView() {
        return getRecyclerView();
    }
    protected void showProgressDialog(String msg) {
        try {
            if (progressDialog == null ) {
                progressDialog = new ProgressDialog(getActivity());
                progressDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            }
            if(!progressDialog.isShowing()) {
                progressDialog.setMessage(msg);
                progressDialog.show();
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    protected void hideProgressDialog() {
        try {
            if (progressDialog != null && progressDialog.isShowing()) {
                progressDialog.dismiss();
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    @Override
    public void onPrivacyLoaded(PrivacySettings item) {

    }

    @Override
    public void onJobsLoaded(List<JobItem> items) {

    }

    protected void openAcitivty(Bundle bundle, Class<?> cls) {
        Intent intent = new Intent(getActivity(), cls);
        intent.putExtra(Constants.DATA, bundle);
        intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_NEW_TASK);
        startActivity(intent);
    }




}
