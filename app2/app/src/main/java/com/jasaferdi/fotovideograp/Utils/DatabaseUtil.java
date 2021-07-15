package com.jasaferdi.fotovideograp.Utils;

import android.support.annotation.NonNull;
import android.text.Html;

import com.jasaferdi.fotovideograp.Model.Countries;
import com.jasaferdi.fotovideograp.Model.Login.User;
import com.jasaferdi.fotovideograp.Model.categories.Category;
import com.jasaferdi.fotovideograp.Model.categories.SubCategory;

import java.util.ArrayList;
import java.util.List;

import io.realm.Realm;
import io.realm.RealmResults;

/**
 * Created by  on 12/24/2017.
 */

public class DatabaseUtil {

    private static Realm realm;
    private  static  DatabaseUtil databaseUtil;

    private DatabaseUtil(){
        realm = Realm.getDefaultInstance();
    }

    public static DatabaseUtil getInstance(){
        if(databaseUtil == null) {
            databaseUtil = new DatabaseUtil();
        }

        return databaseUtil;
    }
    public void storeCategoriesList(final List<Category> categories) {
        realm.executeTransaction(new Realm.Transaction() {
            @Override
            public void execute(Realm realm) {
                realm.insert(categories);
            }
        });
    }

    public void storeUser(final User user) {
        deleteUser();
        realm.executeTransaction(new Realm.Transaction() {
            @Override
            public void execute(Realm realm) {
                realm.insert(user);
            }
        });
    }

    private void deleteUser() {
        realm.executeTransaction(new Realm.Transaction() {
            @Override
            public void execute(Realm realm) {
                RealmResults<User> cat = realm.where(User.class).findAll();
                if(cat != null) {
                    cat.deleteAllFromRealm();
                }
            }
        });
    }

    public void storeCountries(final List<Countries> country) {
        realm.executeTransaction(new Realm.Transaction() {
            @Override
            public void execute(Realm realm) {
                RealmResults<Countries> cat = realm.where(Countries.class).findAll();
                if(cat != null) {
                    cat.deleteAllFromRealm();
                }
            }
        });

        realm.executeTransaction(new Realm.Transaction() {
            @Override
            public void execute(Realm realm) {
                realm.insert(country);
            }
        });
    }
    public User getUser() {
            return realm.where(User.class).findFirst();
    }

    public int getUserID(){
        return getUser().getData().getID();
    }
    public List<Category>  getCategoriesList() {
        return realm.where(Category.class).findAll();
    }

    public List<Category>  getSubCategoriesList(String cat) {
        return realm.where(Category.class).equalTo("title",cat).findAll();
    }

    public void delteCategoriesList() {
        realm.executeTransaction(new Realm.Transaction() {
            @Override
            public void execute(Realm realm) {
                RealmResults<Category> cat = realm.where(Category.class).findAll();
                if(cat != null) {
                    cat.deleteAllFromRealm();
                }
            }
        });

    }


    @NonNull
    public ArrayList<String> getCategories() {
        ArrayList<String> categories = new ArrayList<>();
        List<Category> list = getCategoriesList();
        for(Category category:list){
            categories.add(Html.fromHtml(category.getTitle()).toString());
        }
        return categories;
    }

    @NonNull
    public ArrayList<String> getCountries() {
        ArrayList<String> categories = new ArrayList<>();

        List<Countries> list =   realm.where(Countries.class).findAll();
        for(Countries country:list){
            categories.add(Html.fromHtml(country.getName()).toString());
        }
        return categories;
    }

    @NonNull
    public ArrayList<String> getCities(String name) {
        ArrayList<String> categories = new ArrayList<>();

        List<Countries> list =   realm.where(Countries.class).equalTo("name",name).findAll();

        if(!list.isEmpty()) {
            categories.addAll(list.get(0).getCities());
        }

        return categories;
    }
    @NonNull
    public ArrayList<String> getSubCategories(String cat) {
        ArrayList<String> categories = new ArrayList<>();
        List<Category> list = getCategoriesList();
        Category requiredCat = new Category();
        for (Category category : list){
            if(Html.fromHtml(category.getTitle()).toString().equals(cat)){
                requiredCat = category;
            }
        }
        if(requiredCat.getSubCategories()!= null && !requiredCat.getSubCategories().isEmpty()) {
            for (SubCategory category : requiredCat.getSubCategories()) {
                categories.add(Html.fromHtml(category.getTitle()).toString());
            }
        }
        return categories;
    }

}
