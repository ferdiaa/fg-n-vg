package com.jasaferdi.fotovideograp.DataManager;

import com.jasaferdi.fotovideograp.Interface.DataManager;
import com.jasaferdi.fotovideograp.Interface.OnDataLoadListener;
import com.jasaferdi.fotovideograp.Retrofit.RetrofitUtil;
import com.jasaferdi.fotovideograp.Utils.DatabaseUtil;

import static com.jasaferdi.fotovideograp.Retrofit.RetrofitUtil.getCategries;

/**
 * Created by Gohar Ali on 12/25/2017.
 */

//public class CategoryDataManager implements DataManager {

   // @Override
   // public void loadDataFromServer(OnDataLoadListener listener, boolean isForced) {
   //     if(isForced) {
   //         RetrofitUtil.createProviderAPI().loadCategories().enqueue(getCategries(listener));
   //     }else{
   //         loadDataFromDataBase(listener);
   //     }
   // }
public class CategoryDataManager implements DataManager {
    public void saveDataToDataBase(Object obj) {
    }

    public void loadDataFromServer(OnDataLoadListener onDataLoadListener, boolean z) {
        if (z) {
            RetrofitUtil.createProviderAPI().loadCategories().enqueue(RetrofitUtil.getCategries(onDataLoadListener));
        } else {
            loadDataFromDataBase(onDataLoadListener);
        }
    }

    @Override
    public void loadDataFromDataBase(OnDataLoadListener onDataLoadListener) {
       // listener.onCategoriesLoad(DatabaseUtil.getInstance().getCategoriesList());
        onDataLoadListener.onCategoriesLoad(DatabaseUtil.getInstance().getCategoriesList());
    }

   // @Override
   // public void saveDataToDataBase(Object data) {
       // DatabaseUtil.getInstance().delteCategoriesList();
       // DatabaseUtil.getInstance().storeCategoriesList((List<Category>)(Object)data);
   // }

    @Override
    public void deleteDataToDataBase() {
        DatabaseUtil.getInstance().delteCategoriesList();
    }
}
