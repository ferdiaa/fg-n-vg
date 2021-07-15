package com.jasaferdi.fotovideograp.ViewHolders;

import android.support.v7.widget.RecyclerView;
import android.view.View;
import android.widget.ImageView;
import android.widget.TextView;

import com.jasaferdi.fotovideograp.Model.Appointment;
import com.jasaferdi.fotovideograp.R;

/**
 * Created by  on 2/12/2018.
 */

public class ManageAppointmentViewHolder2 extends RecyclerView.ViewHolder {

    public TextView name, service, type;
    public ImageView avatar;
    public Appointment mItem;
    public View view;


    public ManageAppointmentViewHolder2(View view) {
        super(view);
        this.view = view;
        name =  view.findViewById(R.id.customer_name);
        service =  view.findViewById(R.id.customer_service);
        type = view.findViewById(R.id.customer_service_type);
        avatar = view.findViewById(R.id.provider_thumbail);
    }

}


