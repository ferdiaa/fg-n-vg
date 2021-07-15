package com.jasaferdi.fotovideograp.activities;

import android.os.Bundle;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.text.Html;
import android.view.View;
import android.widget.TextView;

import com.cooltechworks.views.shimmer.ShimmerRecyclerView;
import com.jasaferdi.fotovideograp.R;
import com.jasaferdi.fotovideograp.Utils.Constants;

/*
 * Created by on 12/28/2017.
 */

public class CommonProviderInfoActivity2 extends BaseActivity {


    private RecyclerView recyclerView;
    private TextView noData;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.fragment_provider_list);

        if( getSupportActionBar() != null && getIntent().getBundleExtra(Constants.DATA) != null
                &&getIntent().getBundleExtra(Constants.DATA).getString(Constants.TITLE) != null) {
            getSupportActionBar().setTitle(Html.fromHtml(getIntent().getBundleExtra(Constants.DATA).getString(Constants.TITLE)));
        }

        initViews();
        setAdapter();

    }

    private void initViews() {
        recyclerView = (ShimmerRecyclerView) findViewById(R.id.list);
        noData = findViewById(R.id.list_no_data);

        recyclerView.setLayoutManager(new LinearLayoutManager(this));
        recyclerView.setNestedScrollingEnabled(false);

    }

    protected void setAdapter() {
    }

    protected void showNoData(){
        recyclerView.setAdapter(null);
        noData.setVisibility(View.VISIBLE);
    }

    public RecyclerView getRecyclerView() {
        return recyclerView;
    }

    @Override
    public void onError(Constants.Errors type,String error) {
        super.onError(type,error);
        showNoData();
    }
}
