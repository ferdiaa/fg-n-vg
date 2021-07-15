package com.jasaferdi.fotovideograp.activities;

import com.jasaferdi.fotovideograp.Model.Provider.Qualification;
import com.jasaferdi.fotovideograp.Utils.Constants;
import com.jasaferdi.fotovideograp.adapters.ProviderQualificationAdapter;

import java.util.List;

public class ProviderQualificationActivity extends CommonProviderInfoActivity {

    @Override
    protected void setAdapter() {
        List<Qualification> services = (List<Qualification>) (getIntent().getBundleExtra(Constants.DATA)).getSerializable(Constants.DATA);
        if (services != null && !services.isEmpty()) {
            getRecyclerView().setAdapter(new ProviderQualificationAdapter
                    (services, null));
        }else{
            showNoData();
        }
    }
}
