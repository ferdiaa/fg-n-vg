package com.jasaferdi.fotovideograp.Utils;

import android.content.Context;
import android.content.SharedPreferences;

import static android.content.Context.MODE_PRIVATE;

/**
 * Created by Gohar Ali on 12/26/2017.
 */

public class SharedPreferenceUtil {

    private static String MY_PREFS_NAME = "serviceproviderpref";

    public static void storeStringValue(Context context,String key, String value){
        SharedPreferences.Editor editor = context.getSharedPreferences(MY_PREFS_NAME, MODE_PRIVATE).edit();
        editor.putString(key, value);
        editor.apply();
    }


    public static void storeBooleanValue(Context context,String key, boolean value){
        SharedPreferences.Editor editor = context.getSharedPreferences(MY_PREFS_NAME, MODE_PRIVATE).edit();
        editor.putBoolean(key, value);
        editor.apply();
    }

    public static boolean getBoolen(Context context,String key){
        SharedPreferences prefs = context.getSharedPreferences(MY_PREFS_NAME, MODE_PRIVATE);
        return prefs.getBoolean(key, false);
    }
}
