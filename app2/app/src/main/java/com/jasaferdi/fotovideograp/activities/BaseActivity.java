package com.jasaferdi.fotovideograp.activities;

import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.net.Uri;
import android.os.Bundle;
import android.support.v4.app.ActivityCompat;
import android.support.v4.content.ContextCompat;
import android.support.v7.app.AlertDialog;
import android.support.v7.app.AppCompatActivity;
import android.view.LayoutInflater;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;
import android.widget.SeekBar;
import android.widget.TextView;

import com.google.android.gms.common.GooglePlayServicesNotAvailableException;
import com.google.android.gms.common.GooglePlayServicesRepairableException;
import com.google.android.gms.location.places.Place;
import com.google.android.gms.location.places.ui.PlacePicker;
import com.jasaferdi.fotovideograp.Interface.DialogInteractionListener;
import com.jasaferdi.fotovideograp.Interface.OnDataLoadListener;
import com.jasaferdi.fotovideograp.Interface.OnListInteractionListener;
import com.jasaferdi.fotovideograp.Model.Appointment;
import com.jasaferdi.fotovideograp.Model.Countries;
import com.jasaferdi.fotovideograp.Model.JobItem;
import com.jasaferdi.fotovideograp.Model.Provider.BusinessHours;
import com.jasaferdi.fotovideograp.Model.Provider.PrivacySettings;
import com.jasaferdi.fotovideograp.Model.Provider.ProfileServices;
import com.jasaferdi.fotovideograp.Model.Provider.ProviderModel;
import com.jasaferdi.fotovideograp.Model.Provider.ProviderReviewListData;
import com.jasaferdi.fotovideograp.Model.categories.Category;
import com.jasaferdi.fotovideograp.R;
import com.jasaferdi.fotovideograp.Utils.AppUtils;
import com.jasaferdi.fotovideograp.Utils.Constants;

import java.util.ArrayList;
import java.util.List;

import static com.jasaferdi.fotovideograp.fragments.HomeFragment.PLACE_PICKER_REQUEST;

/**
 * Created by on 11/28/2017.
 */

public class BaseActivity extends AppCompatActivity implements
        OnDataLoadListener, OnListInteractionListener, View.OnClickListener, DialogInteractionListener {


    private ProgressDialog progressDialog;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        overridePendingTransition(android.R.anim.fade_in, android.R.anim.fade_out);

        progressDialog = new ProgressDialog(this);
        progressDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);

        setActionBar();
    }

    protected void setActionBar() {
        if (getSupportActionBar() != null) {
            getSupportActionBar().setDisplayHomeAsUpEnabled(true);
            getSupportActionBar().setDisplayShowHomeEnabled(true);
        }
    }

    protected void showProgressDialog(String msg) {
        try {
            if (progressDialog != null && !progressDialog.isShowing()) {
                progressDialog.setMessage(msg);
                progressDialog.show();
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    protected void hideProgressDialog() {
        try {
            if (progressDialog != null && progressDialog.isShowing()) {
                progressDialog.dismiss();
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        int id = item.getItemId();

        if (id == android.R.id.home) {
            onBackPressed();
            return true;
        }

        return super.onOptionsItemSelected(item);
    }

    @Override
    public void onBackPressed() {
        super.onBackPressed();
        overridePendingTransition(
                R.anim.no_anim, R.anim.slide_right_out);
        AppUtils.hideSoftKeyboard(this);
    }

    @Override
    public void onCategoriesLoad(List<Category> items) {

    }

    @Override
    public void onProviderLoad(List<ProviderModel> items) {

    }

    @Override
    public void onCountriesLoad(List<Countries> items) {

    }

    @Override
    public void onAppointmentsLoad(List<Appointment> items) {

    }

    @Override
    public void onServiceLoad(List<ProfileServices> items) {

    }

    @Override
    public void onReviewsLoad(List<ProviderReviewListData> items) {

    }

    @Override
    public void onUpdateFavorites(ProviderModel item) {

    }

    @Override
    public void onPrivacyLoaded(PrivacySettings item) {

    }

    @Override
    public void onBusinessHoursLoaded(BusinessHours item) {

    }

    @Override
    public void onJobsLoaded(List<JobItem> items) {

    }

    @Override
    public void onSuccess(String msg) {
        try {
            hideProgressDialog();
            AppUtils.showDialog(this, msg, this);
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    @Override
    public void onError(Constants.Errors errorCode, String error) {
        try {
            hideProgressDialog();
            AppUtils.showDialog(this, error, null);
        } catch (Exception e) {
            e.printStackTrace();
        }

    }

    @Override
    public void onProviderListInteraction(ProviderModel item) {

    }

    @Override
    public void onCategoryListInteraction(Category item) {

    }

    @Override
    public void onAppointmentInteraction(Appointment item, int pos) {

    }

    @Override
    public void onProviderFavorite(ProviderModel item) {

    }

    @Override
    public void onJobItemSelection(JobItem item) {

    }

    public void onProfileLoaded(ProviderModel providerModel) {
    }

    @Override
    public void onUserMessageSelection(String path, int post) {

    }

    @Override
    public void removeItem(int position) {

    }

    protected void openAcitivty(Class<?> cls) {
        Intent intent = new Intent(this, cls);
        intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_NEW_TASK);
        startActivity(intent);
    }

    protected void openAcitivty(Intent intent2, Class<?> cls) {
        Intent intent = new Intent(this, cls);
        if (intent2 != null) {
            intent = intent2;
            intent.setClass(this, cls);
        }
        intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_NEW_TASK);
        startActivity(intent);
    }

    protected void openAcitivty(Bundle bundle, Class<?> cls) {
        Intent intent = new Intent(this, cls);
        intent.putExtra(Constants.DATA, bundle);
        intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_NEW_TASK);
        startActivity(intent);
    }

    protected void openAcitivty(ArrayList<String> categories, Class<?> cls, int code, String title, String seletectedItem) {
        Intent newIntent = new Intent(this, cls);
        newIntent.putStringArrayListExtra(Constants.DATA, categories);
        newIntent.putExtra(Constants.TITLE, title);
        newIntent.putExtra(Constants.SELECTED_ITEM,seletectedItem);
        startActivityForResult(newIntent, code);
    }

    protected void openActivityForResult(Class<?> cls) {
        Intent intent = new Intent(this, cls);
        intent.putExtra(Constants.IS_RESULT_ACTIVITY, true);
        intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_NEW_TASK);
        startActivityForResult(intent, Constants.REQUEST_KEY_LOGIN);
    }

    @Override
    public void onClick(View view) {

    }

    protected void showDialogSignedUp(String msg, final DialogInteractionListener listener) {
        AlertDialog.Builder builder = new AlertDialog.Builder(this);
        builder
                .setMessage(msg)
                .setPositiveButton(R.string.title_login, new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int which) {
                        if (listener != null) {
                            listener.onPositiveClick(null);
                        } else {
                            openAcitivty(getIntent(), LoginActivity.class);
                            finish();
                        }
                        dialog.dismiss();

                    }
                }).setNegativeButton(android.R.string.cancel, new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int which) {
                dialog.dismiss();

            }
        }).setIcon(android.R.drawable.ic_dialog_alert)
                .setCancelable(false)
                .show();
    }

    protected void pickLocation() {

        PlacePicker.IntentBuilder builder = new PlacePicker.IntentBuilder();

        try {
            startActivityForResult(builder.build(this), PLACE_PICKER_REQUEST);
        } catch (GooglePlayServicesRepairableException e) {
            e.printStackTrace();
        } catch (GooglePlayServicesNotAvailableException e) {
            e.printStackTrace();
        }
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (requestCode == PLACE_PICKER_REQUEST) {
            if (resultCode == RESULT_OK) {
                Place place = PlacePicker.getPlace(data, this);
                String toastMsg = place.getAddress().toString();
                setLocation(toastMsg, place);

            }
        }

    }

    public void showRadiusDialog(final DialogInteractionListener listener, String val) {

        final AlertDialog.Builder popDialog = new AlertDialog.Builder(this);
        final LayoutInflater inflater = (LayoutInflater) this.getSystemService(LAYOUT_INFLATER_SERVICE);

        final View Viewlayout = inflater.inflate(R.layout.ui_radius_search,
                (ViewGroup) findViewById(R.id.layout_dialog));

        final TextView item1 = (TextView) Viewlayout.findViewById(R.id.txtItem1); // txtItem1

        popDialog.setIcon(android.R.drawable.btn_star_big_on);
        //popDialog.setTitle("Please Select Rank 1-100 ");
        popDialog.setView(Viewlayout);

        //  seekBar1
        final SeekBar seek1 = (SeekBar) Viewlayout.findViewById(R.id.seekBar1);
        if (val != null && !val.isEmpty()) {
            seek1.setProgress(Integer.parseInt(val));
        }
        item1.setText(getString(R.string.distance_in_miles) + val);
        seek1.setOnSeekBarChangeListener(new SeekBar.OnSeekBarChangeListener() {
            public void onProgressChanged(SeekBar seekBar, int progress, boolean fromUser) {
                //Do something here with new value
                item1.setText(getString(R.string.distance_in_miles) + progress);

            }

            public void onStartTrackingTouch(SeekBar arg0) {
                // TODO Auto-generated method stub

            }

            public void onStopTrackingTouch(SeekBar seekBar) {
                // TODO Auto-generated method stub

            }
        });

        // Button OK
        popDialog.setPositiveButton("OK",
                new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int which) {
                        listener.onPositiveClick(seek1.getProgress() + "");
                        dialog.dismiss();
                    }

                });


        popDialog.create();
        popDialog.show();

    }

    @Override
    public void onPositiveClick(String msg) {

    }

    protected void setLocation(String name, Place place) {

    }

    public void makePhoneCall(Context context){

    }

    public void makePhoneCall(Context context, String number) {
        if (ContextCompat.checkSelfPermission(context,
                android.Manifest.permission.CALL_PHONE)
                == PackageManager.PERMISSION_GRANTED) {
            Intent phoneIntent = new Intent(Intent.ACTION_CALL);
            phoneIntent.setData(Uri.parse("tel:" + number));
            context.startActivity(phoneIntent);
        } else {
            getPhoneCallPermission();
        }
    }

    protected void getPhoneCallPermission() {
        if (ContextCompat.checkSelfPermission(this,
                android.Manifest.permission.CALL_PHONE)
                != PackageManager.PERMISSION_GRANTED) {
            ActivityCompat.requestPermissions(this,
                    new String[]{android.Manifest.permission.CALL_PHONE},
                    1);
        }
    }

    @Override
    public void onRequestPermissionsResult(int requestCode, String permissions[], int[] grantResults) {
        switch (requestCode) {
            case 1: {
                if (grantResults.length > 0
                        && grantResults[0] == PackageManager.PERMISSION_GRANTED) {
                    makePhoneCall(this);
                }
                break;
            }
        }
    }

    public void openEmail(String email) {
        Intent intent = new Intent(Intent.ACTION_SENDTO);
        intent.setData(Uri.parse("mailto:"));
        intent.putExtra(Intent.EXTRA_EMAIL, new String[]{email});
        if (intent.resolveActivity(getPackageManager()) != null) {
            startActivity(intent);
        }
    }

}
