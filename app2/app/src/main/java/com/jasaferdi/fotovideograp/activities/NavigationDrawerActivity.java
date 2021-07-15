package com.jasaferdi.fotovideograp.activities;

import android.app.NotificationChannel;
import android.app.NotificationManager;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.support.design.widget.NavigationView;
import android.support.design.widget.Snackbar;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentTransaction;
import android.support.v4.view.GravityCompat;
import android.support.v4.widget.DrawerLayout;
import android.support.v7.app.ActionBarDrawerToggle;
import android.support.v7.app.AlertDialog;
import android.support.v7.widget.Toolbar;
import android.view.MenuItem;
import android.view.View;
import android.widget.ImageView;
import android.widget.TextView;
import android.support.design.widget.NavigationView.OnNavigationItemSelectedListener;

import com.google.firebase.auth.FirebaseAuth;
import com.mikhaellopez.circularimageview.CircularImageView;
import com.squareup.picasso.Picasso;
import com.jasaferdi.fotovideograp.BuildConfig;
import com.jasaferdi.fotovideograp.Model.Login.User;
import com.jasaferdi.fotovideograp.R;
import com.jasaferdi.fotovideograp.Utils.AppUtils;
import com.jasaferdi.fotovideograp.Utils.Constants;
import com.jasaferdi.fotovideograp.Utils.DatabaseUtil;
import com.jasaferdi.fotovideograp.Utils.SharedPreferenceUtil;
import com.jasaferdi.fotovideograp.Utils.StickyService;
import com.jasaferdi.fotovideograp.fragments.HomeFragment;
//import com.jasaferdi.fotovideograp.fragments.JobsListingFragment;
import com.jasaferdi.fotovideograp.fragments.ManageBusinessHourFragment;
import com.jasaferdi.fotovideograp.fragments.ManagePrivacyFragment;
import com.jasaferdi.fotovideograp.fragments.ManageServicesFragment;
import com.jasaferdi.fotovideograp.fragments.MyFavoritesFragment;
import com.jasaferdi.fotovideograp.fragments.MyInboxFragment;


public class NavigationDrawerActivity extends BaseActivity
        implements NavigationView.OnNavigationItemSelectedListener {
    private NavigationView navigationView;

    private static String appUrl = "" ;



    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_navigation_drawer);

        Toolbar toolbar = (Toolbar) findViewById(R.id.toolbar);
        setSupportActionBar(toolbar);

        DrawerLayout drawer = (DrawerLayout) findViewById(R.id.drawer_layout);
        ActionBarDrawerToggle toggle = new ActionBarDrawerToggle(
                this, drawer, toolbar, R.string.navigation_drawer_open, R.string.navigation_drawer_close);
        if(drawer != null) {
            drawer.addDrawerListener(toggle);
            toggle.syncState();
        }

        navigationView = (NavigationView) findViewById(R.id.nav_view);
        navigationView.setNavigationItemSelectedListener(this);

        replaceFragment(HomeFragment.newInstance());

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            // Create channel to show notifications.
            String channelId  = getString(R.string.default_notification_channel_id);
            String channelName = getString(R.string.default_notification_channel_name);
            NotificationManager notificationManager =
                    getSystemService(NotificationManager.class);
            notificationManager.createNotificationChannel(new NotificationChannel(channelId,
                    channelName, NotificationManager.IMPORTANCE_HIGH));
        }

        Intent stickyService = new Intent(this, StickyService.class);
        startService(stickyService);

        if(!AppUtils.isconnected()){
            Snackbar snackbar = Snackbar.make(findViewById(android.R.id.content), getString(R.string.err_no_internet)
                    , Snackbar.LENGTH_LONG);
            snackbar.show();
        }
    }

    @Override
    protected void setActionBar() {

    }

    @Override
    protected void onResume() {
        super.onResume();
        hideShowItems();
    }

    private void hideShowItems() {
        boolean isUserLoggedIn = SharedPreferenceUtil.getBoolen(this, Constants.ISUSERLOGGEDIN);
        navigationView.getMenu().findItem(R.id.nav_register).setVisible(!isUserLoggedIn);
        navigationView.getMenu().findItem(R.id.nav_login).setVisible(!isUserLoggedIn);
        navigationView.getMenu().findItem(R.id.nav_logout).setVisible(isUserLoggedIn);
        navigationView.getMenu().findItem(R.id.nav_appointments).setVisible(isUserLoggedIn);
      //  navigationView.getMenu().findItem(R.id.nav_appointments2).setVisible(isUserLoggedIn);
        navigationView.getMenu().findItem(R.id.nav_manage_services).setVisible(isUserLoggedIn);
     //   navigationView.getMenu().findItem(R.id.nav_favorites_listing).setVisible(isUserLoggedIn);
        //navigationView.getMenu().findItem(R.id.nav_home).setVisible(isUserLoggedIn);
        // navigationView.getMenu().findItem(R.id.nav_inbox).setVisible(isUserLoggedIn);
        navigationView.getMenu().findItem(R.id.nav_dashboard_item).setVisible(isUserLoggedIn);

        navigationView.getMenu().setGroupVisible(R.id.nav_dashboard,isUserLoggedIn);

        if(isUserLoggedIn) {
            View header = navigationView.getHeaderView(0);
            TextView name = (TextView) header.findViewById(R.id.nav_username);
            TextView msg = (TextView) header.findViewById(R.id.nav_user_msg);
            CircularImageView pic = header.findViewById(R.id.user_profile_pic);
            ImageView banner = header.findViewById(R.id.user_banner_image);
            User user = DatabaseUtil.getInstance().getUser();
            if(user != null) {
                name.setText(user.getData().getData().getDisplayName());
                msg.setText(user.getData().getData().getUserEmail());
                Picasso.with(this).load(user.getData().getData().getAvatar()).into(pic);
                Picasso.with(this).load(user.getData().getData().getBanner()).fit().into(banner);
            }
        }

        /*(navigationView.getMenu().findItem(R.id.nav_footer_1)).setTitle(
                getString(R.string.copyright) + "" +
                getString(R.string.version) +BuildConfig.VERSION_NAME);*/
    }

    @Override
    public void onBackPressed() {
        DrawerLayout drawer = (DrawerLayout) findViewById(R.id.drawer_layout);
        if (drawer.isDrawerOpen(GravityCompat.START)) {
            drawer.closeDrawer(GravityCompat.START);
        } else {
            getPrevious();
        }

    }


    private void showExitDialog() {
        AlertDialog.Builder builder = new AlertDialog.Builder(this);
        builder.setMessage(R.string.msg_exit)
                .setCancelable(false)
                .setPositiveButton("Yes", new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int id) {
                        finish();
                    }
                })
                .setNegativeButton("No", new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int id) {
                        dialog.cancel();
                    }
                });
        AlertDialog alert = builder.create();
        alert.show();
    }

    @SuppressWarnings("StatementWithEmptyBody")
    @Override
    public boolean onNavigationItemSelected(MenuItem item) {
        // Handle navigation view item clicks here.
        int id = item.getItemId();

        if (id == R.id.nav_register) {
            openAcitivty(SignupActivity.class);
        } else if (id == R.id.nav_login) {
            openAcitivty(LoginActivity.class);
        } else if (id == R.id.nav_appointments) {
            openAcitivty(MyAppointmentsActivity.class);
        }  else if (id == R.id.nav_aboutus) {
            openWebview(Constants.ABOUTUS,getString(R.string.about_us));
        } else if (id == R.id.nav_contact_support) {
            openWebview(Constants.CONTACTUS,getString(R.string.contact_us));
        } else if (id == R.id.nav_term_of_use) {
            openWebview(Constants.TERMSOFUSE,getString(R.string.terms_of_use));
        }  else if (id == R.id.nav_help_support) {
            openWebview(Constants.HELPSUPPORT,getString(R.string.help_support));
        }else if (id == R.id.nav_logout) {
            confirmLogout();
        }
        //else if(id == R.id.nav_invite){
         //   shareApp();
        //}
        //else if(id == R.id.nav_rate){
         //   showRateDialog(NavigationDrawerActivity.this);
        //}
        else if (id == R.id.nav_manage_services) {
            replaceFragment(new ManageServicesFragment());
        }else if (id == R.id.nav_home) {
            replaceFragment(HomeFragment.newInstance());
        }
        //else if(id == R.id.nav_favorites_listing){
        //    replaceFragment(new MyFavoritesFragment());
       // }
        else if(id == R.id.nav_manage_appointment){
            replaceFragment(new ManageAppointmentFragment());
        }
        //else if(id == R.id.nav_manage_appointment2){
       //     replaceFragment(new ManageAppointmentFragment2());
       // }
        else if(id == R.id.nav_privacy_settings){
            replaceFragment(ManagePrivacyFragment.newInstance());
        }
        //else if(id == R.id.nav_jobs){
          //  replaceFragment(new JobsListingFragment());
        //}
        else if(id == R.id.nav_business_hours){
            replaceFragment(ManageBusinessHourFragment.newInstance());
        }
        //else if(id == R.id.nav_inbox){
        //    replaceFragment(new MyInboxFragment());
        //}

        DrawerLayout drawer = (DrawerLayout) findViewById(R.id.drawer_layout);
        drawer.closeDrawer(GravityCompat.START);
        return true;
    }

    public static void showRateDialog(final Context context) {
        AlertDialog.Builder builder = new AlertDialog.Builder(context)
                .setTitle("Rate application")
                .setMessage("Please, rate the app at play store")
                .setPositiveButton("RATE", new DialogInterface.OnClickListener() {
                    @Override
                    public void onClick(DialogInterface dialog, int which) {
                        if (context != null) {
                            String link = "market://details?id=";
                            try {
                                context.getPackageManager()
                                        .getPackageInfo("com.android.vending", 0);
                            } catch (PackageManager.NameNotFoundException e) {
                                e.printStackTrace();
                                link = appUrl;
                            }
                            context.startActivity(new Intent(Intent.ACTION_VIEW,
                                    Uri.parse(link + context.getPackageName())));
                        }
                    }
                })
                .setNegativeButton("CANCEL", null);
        builder.show();
    }

    private void shareApp() {
        String message = appUrl + BuildConfig.APPLICATION_ID;
        Intent share = new Intent(Intent.ACTION_SEND);
        share.setType("text/plain");
        share.putExtra(Intent.EXTRA_TEXT, message);

        startActivity(Intent.createChooser(share, "Choose "));
    }


    private void replaceFragment(Fragment newFragment) {

        String backStateName = newFragment.getClass().getName();

        FragmentManager manager = getSupportFragmentManager();
        boolean fragmentPopped = manager.popBackStackImmediate (backStateName, 0);

        if (!fragmentPopped){ //fragment not in back stack, create it.
            final FragmentTransaction transaction = getSupportFragmentManager().beginTransaction();
            transaction.setCustomAnimations(android.R.anim.fade_in, android.R.anim.fade_out);
            transaction.replace(R.id.flContentRoot, newFragment);
            transaction.addToBackStack(backStateName);
            transaction.commit();
        }
    }

    private void getPrevious(){
        FragmentManager fm = getSupportFragmentManager();
        if (fm.getBackStackEntryCount() > 1) {
            fm.popBackStack();
        } else {
            showExitDialog();
        }
    }


    private void openWebview(String url,String title){
        AppUtils.openWebview(this,url,title);
    }

    private void confirmLogout(){
        new AlertDialog.Builder(this)
                .setMessage(R.string.msg_logout)
                .setCancelable(false)
                .setPositiveButton(R.string.title_yes, new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int id) {
                        SharedPreferenceUtil.storeBooleanValue(NavigationDrawerActivity.this, Constants.ISUSERLOGGEDIN, false);
                        openAcitivty(LoginActivity.class);
                        FirebaseAuth.getInstance().signOut();
                        finish();
                    }
                })
                .setNegativeButton(R.string.title_no, null)
                .show();
    }


}
