package com.jasaferdi.fotovideograp.adapters;

import android.support.v7.widget.RecyclerView;
import android.text.Html;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;

import com.google.firebase.database.DataSnapshot;
import com.google.firebase.database.DatabaseError;
import com.google.firebase.database.DatabaseReference;
import com.google.firebase.database.FirebaseDatabase;
import com.google.firebase.database.Query;
import com.google.firebase.database.ValueEventListener;
import com.squareup.picasso.Picasso;
import com.jasaferdi.fotovideograp.Interface.OnListInteractionListener;
import com.jasaferdi.fotovideograp.R;
import com.jasaferdi.fotovideograp.Utils.AppUtils;
import com.jasaferdi.fotovideograp.Utils.DatabaseUtil;
import com.jasaferdi.fotovideograp.chat.FriendlyMessage;
import com.jasaferdi.fotovideograp.chat.UserObject;

import java.util.List;

import de.hdodenhof.circleimageview.CircleImageView;

import static com.jasaferdi.fotovideograp.chat.ChatActivity.MESSAGE_CHILD;
import static com.jasaferdi.fotovideograp.chat.ChatActivity.META_DATA;
import static com.jasaferdi.fotovideograp.chat.ChatActivity.THREAD_CHILD;
import static com.jasaferdi.fotovideograp.chat.ChatActivity.USERS_CHILD;

/**
 * Created by Confiz123 on 11/29/2017.
 */

public class MyInboxAdapter extends RecyclerView.Adapter<MyInboxAdapter.ViewHolder> {
    private final List<String> mValues;
    private final OnListInteractionListener mListener;

    public MyInboxAdapter(List<String> items,
                          OnListInteractionListener listener) {
        mValues = items;
        mListener = listener;
    }

    @Override
    public MyInboxAdapter.ViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.item_inbox_user, parent, false);
        return new MyInboxAdapter.ViewHolder(view);
    }

    @Override
    public void onBindViewHolder(final MyInboxAdapter.ViewHolder holder,final int position) {
        // holder.mItem = mValues.get(position);
        DatabaseReference databaseReference = FirebaseDatabase.getInstance().getReference();
        String providerID = AppUtils.getProviderId(mValues.get(position), DatabaseUtil.getInstance().getUserID() + "");
        databaseReference.child(USERS_CHILD).child(providerID).child(META_DATA)
                .addValueEventListener(new ValueEventListener() {
            @Override
            public void onDataChange(DataSnapshot dataSnapshot) {
                        UserObject message = dataSnapshot.getValue(UserObject.class);
                        if(message.isOnline())
                        holder.messengerTextView.setText(Html.fromHtml(message.getName()) +" " +  "\u2022");
                        else
                            holder.messengerTextView.setText(Html.fromHtml(message.getName()) );

                Picasso.with(holder.messengerImageView.getContext()).load(message.getImageUrl()).
                                into(holder.messengerImageView);
            }
            @Override
            public void onCancelled(DatabaseError databaseError) {
                //Handle possible errors.
            }
        });
        Query lastQuery = databaseReference.child(THREAD_CHILD).child(mValues.get(position))
                .child(MESSAGE_CHILD).orderByKey().limitToLast(1);
        lastQuery.addValueEventListener(new ValueEventListener() {
            @Override
            public void onDataChange(DataSnapshot dataSnapshot) {
                for(DataSnapshot dp : dataSnapshot.getChildren()) {
                    FriendlyMessage message = dp.getValue(FriendlyMessage.class);
                    if(message.getText() != null)
                    holder.messageTextView.setText(Html.fromHtml(message.getText()));
                }
            }

            @Override
            public void onCancelled(DatabaseError databaseError) {
                //Handle possible errors.
            }
        });

        holder.itemView.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                if(mListener != null)
                mListener.onUserMessageSelection(mValues.get(position),position);
            }
        });

    }

    @Override
    public int getItemCount() {
        return mValues.size();
    }

    public class ViewHolder extends RecyclerView.ViewHolder {
        TextView messageTextView;
        ImageView messageImageView;
        TextView messengerTextView;
        CircleImageView messengerImageView;

        public ViewHolder(View v) {
            super(v);
            messageTextView = (TextView) itemView.findViewById(R.id.messageTextView);
            messageImageView = (ImageView) itemView.findViewById(R.id.messageImageView);
            messengerTextView = (TextView) itemView.findViewById(R.id.messengerTextView);
            messengerImageView = (CircleImageView) itemView.findViewById(R.id.messengerImageView);
        }

        @Override
        public String toString() {
            return super.toString() + " '" + messageTextView.getText() + "'";
        }
    }
}
