package com.jasaferdi.fotovideograp.activities;

import com.jasaferdi.fotovideograp.Model.Provider.ProviderReviewListData;
import com.jasaferdi.fotovideograp.Model.ReviewProvider;
import com.jasaferdi.fotovideograp.Retrofit.RetrofitUtil;
import com.jasaferdi.fotovideograp.Utils.Constants;
import com.jasaferdi.fotovideograp.adapters.ProviderReviewAdapter;

import java.util.List;

import static com.jasaferdi.fotovideograp.Retrofit.RetrofitUtil.getProviderReviews;

public class ProviderReviewsActivity extends CommonProviderInfoActivity {

    @Override
    protected void setAdapter() {
        if(getIntent().getBundleExtra(Constants.DATA) != null){
            ReviewProvider provider = new ReviewProvider();
            provider.setProviderId(getIntent().getBundleExtra(Constants.DATA).getInt(Constants.ID));
            RetrofitUtil.createProviderAPI().getProviderReviews(provider).enqueue(getProviderReviews(this));
        }
    }

    @Override
    public void onReviewsLoad(List<ProviderReviewListData> items) {
        if (items != null && !items.isEmpty()) {
            getRecyclerView().setAdapter(new ProviderReviewAdapter
                    (items));
        } else {
            showNoData();
        }
    }
}
