package com.jasaferdi.fotovideograp.Interface;

import com.jasaferdi.fotovideograp.Model.Appointment;
import com.jasaferdi.fotovideograp.Model.JobItem;
import com.jasaferdi.fotovideograp.Model.Provider.ProviderModel;
import com.jasaferdi.fotovideograp.Model.categories.Category;

/**
 * Created by Gohar Ali on 12/13/2017.
 */

public interface OnListInteractionListener  {

     void onProviderListInteraction(ProviderModel item) ;

     void onCategoryListInteraction(Category item) ;

     void onAppointmentInteraction(Appointment item, int pos) ;

     void onProviderFavorite(ProviderModel item);

     void onJobItemSelection(JobItem item);

     void onUserMessageSelection(String path, int post);

     void removeItem(int position);
}
