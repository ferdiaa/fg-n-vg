package com.jasaferdi.fotovideograp.adapters;

import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

import com.jasaferdi.fotovideograp.Interface.OnListInteractionListener;
import com.jasaferdi.fotovideograp.Model.Appointment;
import com.jasaferdi.fotovideograp.R;
import com.jasaferdi.fotovideograp.ViewHolders.ManageAppointmentViewHolder2;
import com.squareup.picasso.Picasso;

import java.util.List;

/**
 * {@link RecyclerView.Adapter} that can display a {@link } and makes a call to the
 */
public class ManageAppointmentsAdapter2 extends RecyclerView.Adapter<ManageAppointmentViewHolder2> {

    private List<Appointment> mValues;
    private OnListInteractionListener listener;

    public ManageAppointmentsAdapter2(List<Appointment> items, OnListInteractionListener listener) {
        mValues = items;
        this.listener = listener;
    }

    public List<Appointment> getList(){
        return mValues;
    }

    public void removeItem(int position){
        mValues.remove(position);
        notifyItemRemoved(position);
        notifyItemRangeChanged(position,mValues.size()-1);
    }

    @Override
    public ManageAppointmentViewHolder2 onCreateViewHolder(ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.manage_appointment_item2, parent, false);
        return new ManageAppointmentViewHolder2(view);
    }

    @Override
    public void onBindViewHolder(final ManageAppointmentViewHolder2 holder, final int position) {
        holder.mItem = mValues.get(position);

        holder.name.setText(mValues.get(position).getUsername());
        //holder.name.setText(mValues.get(position).getProvider());
        holder.service.setText("Pembayaran: " + mValues.get(position).getAptServices());
        holder.type.setText(": " + mValues.get(position).getAptTypes());

        Picasso.with(holder.avatar.getContext()).load(mValues.get(position).getAvatar()).into(holder.avatar);

        holder.view.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                if(listener != null){
                    listener.onAppointmentInteraction(holder.mItem,position );
                }
            }
        });
    }

    public List<Appointment> getDataList(){
        return mValues;
    }

    @Override
    public int getItemCount() {
        return mValues.size();
    }

}
