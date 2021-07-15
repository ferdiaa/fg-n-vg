package com.jasaferdi.fotovideograp.activities;

import com.jasaferdi.fotovideograp.Model.Provider.ProfileServices;
import com.jasaferdi.fotovideograp.Utils.Constants;
import com.jasaferdi.fotovideograp.adapters.ProviderServicesRecyclerViewAdapter;

import java.util.List;

public class ProviderServicesActivity extends CommonProviderInfoActivity {

    @Override
    protected void setAdapter() {
        List<ProfileServices> services = (List<ProfileServices>) (getIntent().getBundleExtra(Constants.DATA)).getSerializable(Constants.DATA);
        if (services != null && !services.isEmpty()) {
            getRecyclerView().setAdapter(new ProviderServicesRecyclerViewAdapter
                    (services, null));
        } else {
            showNoData();
        }
    }
}
