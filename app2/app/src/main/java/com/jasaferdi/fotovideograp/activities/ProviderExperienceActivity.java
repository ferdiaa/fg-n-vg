package com.jasaferdi.fotovideograp.activities;

import com.jasaferdi.fotovideograp.Model.Provider.Experience;
import com.jasaferdi.fotovideograp.Utils.Constants;
import com.jasaferdi.fotovideograp.adapters.ProviderExperienceAdapter;

import java.util.List;

public class ProviderExperienceActivity extends CommonProviderInfoActivity {

    @Override
    protected void setAdapter() {
        List<Experience> services = (List<Experience>) (getIntent().getBundleExtra(Constants.DATA)).getSerializable(Constants.DATA);
        if (services != null && !services.isEmpty()) {
            getRecyclerView().setAdapter(new ProviderExperienceAdapter
                    (services, null));
        }else{
            showNoData();
        }
    }
}
