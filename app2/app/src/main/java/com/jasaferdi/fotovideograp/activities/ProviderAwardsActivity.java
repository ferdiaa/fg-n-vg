package com.jasaferdi.fotovideograp.activities;

import com.jasaferdi.fotovideograp.Model.Provider.Award;
import com.jasaferdi.fotovideograp.Utils.Constants;
import com.jasaferdi.fotovideograp.adapters.ProviderAwardsAdapter;

import java.util.List;

public class ProviderAwardsActivity extends CommonProviderInfoActivity {

    @Override
    protected void setAdapter() {
        List<Award> services = (List<Award>) (getIntent().getBundleExtra(Constants.DATA)).getSerializable(Constants.DATA);
        if (services != null && !services.isEmpty()) {
            getRecyclerView().setAdapter(new ProviderAwardsAdapter
                    (services, null));
        }else{
            showNoData();
        }
    }
}
