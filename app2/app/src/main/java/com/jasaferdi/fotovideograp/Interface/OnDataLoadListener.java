package com.jasaferdi.fotovideograp.Interface;

import com.jasaferdi.fotovideograp.Model.Appointment;
import com.jasaferdi.fotovideograp.Model.Countries;
import com.jasaferdi.fotovideograp.Model.JobItem;
import com.jasaferdi.fotovideograp.Model.Provider.BusinessHours;
import com.jasaferdi.fotovideograp.Model.Provider.PrivacySettings;
import com.jasaferdi.fotovideograp.Model.Provider.ProfileServices;
import com.jasaferdi.fotovideograp.Model.Provider.ProviderModel;
import com.jasaferdi.fotovideograp.Model.Provider.ProviderReviewListData;
import com.jasaferdi.fotovideograp.Model.categories.Category;
import com.jasaferdi.fotovideograp.Utils.Constants;

import java.util.List;

/**
 * Created by on 12/14/2017.
 */

public interface OnDataLoadListener {

    void onCategoriesLoad(List<Category> items);

    void onProviderLoad(List<ProviderModel> items);

    void onCountriesLoad(List<Countries> items);

    void onAppointmentsLoad(List<Appointment> items);

    void onServiceLoad(List<ProfileServices> items);

    void onReviewsLoad(List<ProviderReviewListData> items);

    void onUpdateFavorites(ProviderModel item);

    void onPrivacyLoaded(PrivacySettings item);

    void onBusinessHoursLoaded(BusinessHours item);

    void onJobsLoaded(List<JobItem> items);

    void onSuccess(String msg);

    void onError(Constants.Errors errorCode, String error);
}
