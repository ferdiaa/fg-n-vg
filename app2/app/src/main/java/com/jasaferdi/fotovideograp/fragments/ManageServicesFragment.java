package com.jasaferdi.fotovideograp.fragments;

import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.TextView;

import com.cooltechworks.views.shimmer.ShimmerRecyclerView;
import com.jasaferdi.fotovideograp.Model.ManageServicesRequestParam;
import com.jasaferdi.fotovideograp.Model.Provider.ProfileServices;
import com.jasaferdi.fotovideograp.R;
import com.jasaferdi.fotovideograp.Retrofit.RetrofitUtil;
import com.jasaferdi.fotovideograp.Utils.Constants;
import com.jasaferdi.fotovideograp.Utils.DatabaseUtil;
import com.jasaferdi.fotovideograp.activities.NavigationDrawerActivity;
import com.jasaferdi.fotovideograp.adapters.ManageServicesAdapter;

import java.util.ArrayList;
import java.util.List;

import static com.jasaferdi.fotovideograp.Retrofit.RetrofitUtil.getProviderServices;
import static com.jasaferdi.fotovideograp.Retrofit.RetrofitUtil.sendRequest;

/**
 * Created by on 2/21/2018.
 */

public class ManageServicesFragment extends BaseFragment {

    private ShimmerRecyclerView recyclerView;
    private TextView addService;
    private ManageServicesAdapter manageServicesAdapter;
    private Button submit;
    private int uid;
    private List<ProfileServices> data;
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {

        View view = inflater.inflate(R.layout.ui_manage_services, container, false);

        ((NavigationDrawerActivity) getActivity()).getSupportActionBar().setTitle("Kelola Pembayaran");

        recyclerView = view.findViewById(R.id.list);
        addService = view.findViewById(R.id.manage_service_add_item);
        submit = view.findViewById(R.id.manage_service_submit);

        if(DatabaseUtil.getInstance().getUser() != null)
        uid = DatabaseUtil.getInstance().getUser().getData().getID();

        addService.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                manageServicesAdapter.addService(new ProfileServices());
            }
        });

        submit.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                List<ProfileServices> services = manageServicesAdapter.getList();
                ManageServicesRequestParam param = new ManageServicesRequestParam
                        (uid,services);

                showProgressDialog(getString(R.string.msg_update_services));
                RetrofitUtil.createProviderAPI().updateUserServices(param).enqueue
                        (sendRequest(ManageServicesFragment.this));
            }
        });

        RetrofitUtil.createProviderAPI().getUserServices(uid)
                .enqueue(getProviderServices(this));

        return view;
    }

    @Override
    public void onError(Constants.Errors errorCode, String error) {
        super.onError(errorCode, error);
        onServiceLoad(new ArrayList<ProfileServices>());
    }

    @Override
    public void onServiceLoad(List<ProfileServices> items) {
        data = items;
        manageServicesAdapter = new ManageServicesAdapter(items,this);
        recyclerView.setAdapter(manageServicesAdapter);
    }

    @Override
    public void removeItem(int position) {
        super.removeItem(position);
        if(data != null && position < data.size()) {
            data.remove(position);
            manageServicesAdapter = new ManageServicesAdapter(data, this);
            recyclerView.setAdapter(manageServicesAdapter);
        }
    }
}
