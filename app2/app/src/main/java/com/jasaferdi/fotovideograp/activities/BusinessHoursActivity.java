package com.jasaferdi.fotovideograp.activities;

import com.jasaferdi.fotovideograp.Model.Provider.BusinessHours;
import com.jasaferdi.fotovideograp.Utils.Constants;
import com.jasaferdi.fotovideograp.adapters.ProviderBusinessHoursAdapter;

public class BusinessHoursActivity extends CommonProviderInfoActivity {
    @Override
    protected void setAdapter() {
        BusinessHours services = (BusinessHours)(getIntent().getBundleExtra(Constants.DATA)).getSerializable(Constants.DATA);
        if(services != null) {
            getRecyclerView().setAdapter(new ProviderBusinessHoursAdapter
                    (services, null));
        }else{
            showNoData();
        }
    }
}
