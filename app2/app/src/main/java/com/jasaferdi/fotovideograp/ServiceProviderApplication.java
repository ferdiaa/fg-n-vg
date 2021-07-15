package com.jasaferdi.fotovideograp;

import android.app.Application;

import com.google.firebase.analytics.FirebaseAnalytics;
import com.google.firebase.iid.FirebaseInstanceId;
import com.jasaferdi.fotovideograp.Utils.AppUtils;

import io.realm.Realm;
import io.realm.RealmConfiguration;

/**
 * Created by Gohar Ali on 12/14/2017.
 */

public class ServiceProviderApplication extends Application {

    public static final int SCHEMA_VERSION = 0;
    private static FirebaseAnalytics mFirebaseAnalytics;
    private static ServiceProviderApplication instance;
    @Override
    public void onCreate() {
        super.onCreate();

        Realm.init(this);
        RealmConfiguration config = new RealmConfiguration.Builder()
                .name("myrealm.realm")
                .schemaVersion(SCHEMA_VERSION)
                .deleteRealmIfMigrationNeeded()
                .build();
        Realm.setDefaultConfiguration(config);

        instance = this;
        mFirebaseAnalytics = FirebaseAnalytics.getInstance(this);

        AppUtils.sendUserTokenRequest(FirebaseInstanceId.getInstance().getToken());

    }

    public static ServiceProviderApplication getInstance(){
        return instance;
    }

    public static FirebaseAnalytics getFirebaseAnalytics(){
        return mFirebaseAnalytics;
    }

}