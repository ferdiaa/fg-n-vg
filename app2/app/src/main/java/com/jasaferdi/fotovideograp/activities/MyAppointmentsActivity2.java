package com.jasaferdi.fotovideograp.activities;

import com.jasaferdi.fotovideograp.Model.Appointment;
import com.jasaferdi.fotovideograp.Model.Login.User;
import com.jasaferdi.fotovideograp.Retrofit.RetrofitUtil;
import com.jasaferdi.fotovideograp.Utils.AppUtils;
import com.jasaferdi.fotovideograp.Utils.DatabaseUtil;
import com.jasaferdi.fotovideograp.adapters.MyAppointmentsRecyclerViewAdapter2;

import java.util.List;

import static com.jasaferdi.fotovideograp.Retrofit.RetrofitUtil.getUserAppointments;


public class MyAppointmentsActivity2 extends CommonProviderInfoActivity {

    @Override
    public void onAppointmentsLoad(List<Appointment> items) {
        if(items != null && items.size() >0) {
            getRecyclerView().setAdapter(new MyAppointmentsRecyclerViewAdapter2(items,this));
        }else{
            showNoData();
        }
    }

    @Override
    protected void setAdapter() {
        User user = DatabaseUtil.getInstance().getUser();
        RetrofitUtil.createProviderAPI().getUserAppointments(user.getData().getID()).
                enqueue(getUserAppointments(MyAppointmentsActivity2.this));
    }

    @Override
    public void onAppointmentInteraction(Appointment item, int pos) {
        AppUtils.showDialog(this,item.toString(),null);
    }
}
