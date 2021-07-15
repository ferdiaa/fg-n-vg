package com.jasaferdi.fotovideograp.activities;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.EditText;

import com.jasaferdi.fotovideograp.Model.ResetPassword;
import com.jasaferdi.fotovideograp.R;
import com.jasaferdi.fotovideograp.Retrofit.RetrofitUtil;

import static com.jasaferdi.fotovideograp.Retrofit.RetrofitUtil.recoverPassword;

public class RecoverPasswordActivity extends BaseActivity {

    private EditText email;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_recover_password);

        email = findViewById(R.id.email);

        getSupportActionBar().setTitle("Recover Password");
        findViewById(R.id.signup).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                startActivity(new Intent(RecoverPasswordActivity.this,SignupActivity.class));
            }
        });

        findViewById(R.id.email_recover).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                showProgressDialog("Please Wait...");
                String em = email.getText().toString();
                if(!em.isEmpty()) {
                    ResetPassword resetPassword = new ResetPassword(em);
                    RetrofitUtil.createProviderAPI().recoverPassword(resetPassword).enqueue(recoverPassword(RecoverPasswordActivity.this));
                }else{
                    email.setError("This field is required!");
                }
            }
        });
    }

    @Override
    public void onPositiveClick(String msg) {
        finish();
    }

}
