package com.jasaferdi.fotovideograp.Model.Login;


import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

import io.realm.RealmList;
import io.realm.RealmObject;

/**
 * Created by Gohar Ali on 1/31/2018.
 */

public class Meta extends RealmObject {

    @SerializedName("phone")
    @Expose
    private RealmList<String> phone = null;

    public RealmList<String> getPhone() {
        return phone;
    }

    public void setPhone(RealmList<String> phone) {
        this.phone = phone;
    }

}