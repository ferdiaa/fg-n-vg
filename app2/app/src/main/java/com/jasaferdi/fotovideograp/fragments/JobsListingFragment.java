package com.jasaferdi.fotovideograp.fragments;

import android.os.Bundle;
import android.support.v7.widget.LinearLayoutManager;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import com.cooltechworks.views.shimmer.ShimmerRecyclerView;
import com.jasaferdi.fotovideograp.Model.JobItem;
import com.jasaferdi.fotovideograp.R;
import com.jasaferdi.fotovideograp.Retrofit.RetrofitUtil;
import com.jasaferdi.fotovideograp.Utils.Constants;
import com.jasaferdi.fotovideograp.activities.JobDetailActivity;
import com.jasaferdi.fotovideograp.activities.NavigationDrawerActivity;
import com.jasaferdi.fotovideograp.adapters.JobsListingAdapter;

import java.util.List;

import static com.jasaferdi.fotovideograp.Retrofit.RetrofitUtil.getAllJobs;

/**
 * Created by Gohar Ali on 2/21/2018.
 */

public class JobsListingFragment extends BaseFragment {

    private ShimmerRecyclerView recyclerView;
    private JobsListingAdapter jobsListingAdapter;
    private TextView noData;
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {

        View view = inflater.inflate(R.layout.fragment_provider_list, container, false);

        ((NavigationDrawerActivity) getActivity()).getSupportActionBar().setTitle("Jobs");

        recyclerView = view.findViewById(R.id.list);
        recyclerView.setLayoutManager(new LinearLayoutManager(getActivity()));
        noData = view.findViewById(R.id.list_no_data);


        RetrofitUtil.createProviderAPI().getAllJobs()
                    .enqueue(getAllJobs(this));

        return view;
    }

    @Override
    public void onJobsLoaded(List<JobItem> items) {
        if(items != null && items.size() > 0) {
            jobsListingAdapter = new JobsListingAdapter(items, this);
            recyclerView.setAdapter(jobsListingAdapter);
        }else{
            recyclerView.setAdapter(null);
            noData.setVisibility(View.VISIBLE);
        }
    }

    public void onJobItemSelection(JobItem item){
        Bundle bundle = new Bundle();
        bundle.putSerializable(Constants.DATA,item);

        openAcitivty(bundle, JobDetailActivity.class);
    }

}
