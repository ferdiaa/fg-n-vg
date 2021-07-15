package com.jasaferdi.fotovideograp.activities;

import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.support.v7.app.AppCompatActivity;

import com.jasaferdi.fotovideograp.R;
import com.jasaferdi.fotovideograp.Utils.Constants;
import com.jasaferdi.fotovideograp.Utils.SharedPreferenceUtil;

public class SplashScreen extends AppCompatActivity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_splash_screen);

        final Handler handler = new Handler();
        handler.postDelayed(new Runnable() {
            @Override
            public void run() {
                if(!SharedPreferenceUtil.getBoolen(SplashScreen.this, Constants.ISUSERLOGGEDIN)) {
                    startActivity(new Intent(SplashScreen.this, WizardActivty.class));
                }else{
                    startActivity(new Intent(SplashScreen.this, NavigationDrawerActivity.class));
                }

                finish();
            }
        }, 800);
    }
}
