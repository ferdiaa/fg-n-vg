package com.jasaferdi.fotovideograp.activities;

import android.content.Intent;
import android.os.Bundle;

import com.jasaferdi.fotovideograp.DataManager.CategoryDataManager;
import com.jasaferdi.fotovideograp.Model.categories.Category;
import com.jasaferdi.fotovideograp.Utils.Constants;
import com.jasaferdi.fotovideograp.Utils.DatabaseUtil;
import com.jasaferdi.fotovideograp.adapters.CategoryRecyclerViewAdapter;

import java.util.List;

/**
 * A fragment representing a list of Items.
 * <p/>
 * interface.
 */
//public class CategoryListActivity extends CommonProviderInfoActivity {
//
//    @Override
//    protected void setAdapter() {
//        List<Category> categoriesList = DatabaseUtil.getInstance().getCategoriesList();
//
//        if (categoriesList != null && !categoriesList.isEmpty()) {
//            onCategoriesLoad(categoriesList);
//        }else{
//            new CategoryDataManager().loadDataFromServer(this,true);
//        }
//    }
public class CategoryListActivity extends CommonProviderInfoActivity {
    protected void setAdapter() {
        List categoriesList = DatabaseUtil.getInstance().getCategoriesList();
        if (categoriesList == null || categoriesList.isEmpty()) {
            new CategoryDataManager().loadDataFromServer(this, true);
        } else {
            onCategoriesLoad(categoriesList);
        }
    }

//    @Override
//    public void onCategoryListInteraction(Category item) {
//        Intent newIntent = new Intent(this, ProviderListActivity.class);
//        Bundle bundle = new Bundle();
//        bundle.putString(Constants.CATEGORY, item.getTitle());
//        bundle.putString(Constants.TITLE,item.getTitle());
//        newIntent.putExtra(Constants.DATA, bundle);
//        newIntent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_NEW_TASK);
//        startActivity(newIntent);
//    }

    public void onCategoryListInteraction(Category category) {
        Intent intent = new Intent(this, ProviderListActivity.class);
        Bundle bundle = new Bundle();
        bundle.putString(Constants.CATEGORY, category.getTitle());
        bundle.putInt(Constants.CATEGORY_ID, category.getId().intValue());
        bundle.putString("title", category.getTitle());
        intent.putExtra("data", bundle);
        intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_NEW_TASK);
        startActivity(intent);
    }

    @Override
    public void onCategoriesLoad(List<Category> items) {
        getRecyclerView().setAdapter(new CategoryRecyclerViewAdapter(items, this));
    }

}
