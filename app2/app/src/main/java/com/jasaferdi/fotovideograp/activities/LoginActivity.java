package com.jasaferdi.fotovideograp.activities;

import android.content.Intent;
import android.os.Bundle;
import android.support.annotation.NonNull;
import android.text.TextUtils;
import android.view.KeyEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.inputmethod.EditorInfo;
import android.widget.AutoCompleteTextView;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

import com.google.android.gms.tasks.OnCompleteListener;
import com.google.android.gms.tasks.Task;
import com.google.firebase.auth.AuthResult;
import com.google.firebase.auth.FirebaseAuth;
import com.google.firebase.auth.FirebaseUser;
import com.google.firebase.iid.FirebaseInstanceId;
import com.jasaferdi.fotovideograp.Interface.OnSignupLoginListener;
import com.jasaferdi.fotovideograp.Model.Login.User;
import com.jasaferdi.fotovideograp.Model.LoginData;
import com.jasaferdi.fotovideograp.R;
import com.jasaferdi.fotovideograp.Retrofit.RetrofitUtil;
import com.jasaferdi.fotovideograp.Utils.AppUtils;
import com.jasaferdi.fotovideograp.Utils.Constants;
import com.jasaferdi.fotovideograp.Utils.SharedPreferenceUtil;
import com.jasaferdi.fotovideograp.Utils.UtilFirebaseAnalytics;

import static com.jasaferdi.fotovideograp.Retrofit.RetrofitUtil.loginUser;

/**
 * A login screen that offers login via email/password.
 */
public class LoginActivity extends BaseActivity implements OnSignupLoginListener {

    // UI references.
    private AutoCompleteTextView mEmailView;
    private EditText mPasswordView;
    private TextView recoverPassword;
    private Button mEmailSignInButton;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login);

        getSupportActionBar().setTitle(R.string.sign_in_now);

        initViews();
        setListener();

    }

    private void setListener() {
        mPasswordView.setOnEditorActionListener(new TextView.OnEditorActionListener() {
            @Override
            public boolean onEditorAction(TextView textView, int id, KeyEvent keyEvent) {
                if (id == R.id.login || id == EditorInfo.IME_NULL) {
                    attemptLogin();
                    return true;
                }
                return false;
            }
        });


        recoverPassword.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View view) {
                startActivity(new Intent(LoginActivity.this,RecoverPasswordActivity.class));
            }
        });

        findViewById(R.id.view_signup).setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View view) {
                openAcitivty(getIntent(),SignupActivity.class);
            }
        });
        findViewById(R.id.signup).setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View view) {
                openAcitivty(getIntent(),SignupActivity.class);
            }
        });


        mEmailSignInButton.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View view) {
                attemptLogin();
            }
        });
    }

    private void initViews() {
        mEmailView = (AutoCompleteTextView) findViewById(R.id.email);
        mPasswordView = (EditText) findViewById(R.id.password);
        recoverPassword = findViewById(R.id.view_forgot_password);
        mEmailSignInButton = (Button) findViewById(R.id.email_sign_in_button);
    }

    @Override
    public void onBackPressed() {
        super.onBackPressed();
        if (isTaskRoot()) {
            openAcitivty(NavigationDrawerActivity.class);
        }

    }
    /**
     * Attempts to sign in or register the account specified by the login form.
     * If there are form errors (invalid email, missing fields, etc.), the
     * errors are presented and no actual login attempt is made.
     */
    private void attemptLogin() {


        // Reset errors.
        mEmailView.setError(null);
        mPasswordView.setError(null);

        // Store values at the time of the login attempt.
        String email = mEmailView.getText().toString();
        String password = mPasswordView.getText().toString();

        boolean cancel = false;
        View focusView = null;

        // Check for a valid password, if the user entered one.
        if (TextUtils.isEmpty(password)) {
            mPasswordView.setError(getString(R.string.error_field_required));
            focusView = mPasswordView;
            cancel = true;
        }

        // Check for a valid email address.
        if (TextUtils.isEmpty(email)) {
            mEmailView.setError(getString(R.string.error_field_required));
            focusView = mEmailView;
            cancel = true;
        } else if (!isEmailValid(email)) {
            mEmailView.setError(getString(R.string.error_invalid_email));
            focusView = mEmailView;
            cancel = true;
        }

        if (cancel) {
            focusView.requestFocus();
        } else {
            showProgressDialog(getString(R.string.logging_in));
            LoginData login = new LoginData(email,password);
            RetrofitUtil.createProviderAPI().loginUser(login).enqueue(loginUser(this));
        }
    }

    private boolean isEmailValid(String email) {
        //TODO: Replace this with your own logic
        return email.contains("@");
    }



    @Override
    public void onSignup(User data) {

    }

    @Override
    public void onLoginUser(User data) {

        if(data.getType().equals(Constants.SUCCESS)){
            AppUtils.sendUserTokenRequest(FirebaseInstanceId.getInstance().getToken());
            UtilFirebaseAnalytics.logEvent(Constants.EVENT_LOGIN,Constants.KEY_EMAIL,mEmailView.getText().toString());
            SharedPreferenceUtil.storeBooleanValue(this,Constants.ISUSERLOGGEDIN,true);
            if(getIntent() != null && getIntent().getBooleanExtra(Constants.IS_RESULT_ACTIVITY,false)){
                setResult(RESULT_OK);
            }else{
                openAcitivty(NavigationDrawerActivity.class);
            }
            loginOnFirebase();
            //finish();
        }
    }

    private void loginOnFirebase(){
        final FirebaseAuth mAuth = FirebaseAuth.getInstance();
        FirebaseUser currentUser = mAuth.getCurrentUser();
        if(currentUser == null){
            final String email = mEmailView.getText().toString();
            final String password = mPasswordView.getText().toString();
            mAuth.signInWithEmailAndPassword(email, password)
                    .addOnCompleteListener(this, new OnCompleteListener<AuthResult>() {
                        @Override
                        public void onComplete(@NonNull Task<AuthResult> task) {
                            if (!task.isSuccessful()) {
                                mAuth.createUserWithEmailAndPassword(email, password)
                                        .addOnCompleteListener(LoginActivity.this, new OnCompleteListener<AuthResult>() {
                                            @Override
                                            public void onComplete(@NonNull Task<AuthResult> task) {
                                                      hideProgressDialog();
                                                    finish();
                                            }
                                        });
                            }else{
                                hideProgressDialog();
                                finish();
                            }
                        }
                    });
        }

    }

    @Override
    public void OnError(String error) {
        hideProgressDialog();
        Toast.makeText(this,error,Toast.LENGTH_LONG).show();
    }
}

