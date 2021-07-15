package com.jasaferdi.fotovideograp.activities;

import android.content.Intent;
import android.os.Bundle;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.view.View;
import android.widget.TextView;

import com.jasaferdi.fotovideograp.Model.SelectableItem;
import com.jasaferdi.fotovideograp.R;
import com.jasaferdi.fotovideograp.Utils.Constants;
import com.jasaferdi.fotovideograp.ViewHolders.SelectableViewHolder;
import com.jasaferdi.fotovideograp.adapters.SelectableRecyclerViewAdapter;

import java.util.ArrayList;
import java.util.List;

/**
 * Created by Gohar Ali on 12/25/2017.
 */

public class SelectableItemActivity extends BaseActivity implements SelectableViewHolder.OnItemSelectedListener {

    RecyclerView recyclerView;
    SelectableRecyclerViewAdapter adapter;
    TextView clearSelection;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_item_selectable);
        initViews();
        setAdapter();

        String title = getIntent().getStringExtra(Constants.TITLE);
        getSupportActionBar().setTitle(title != null && !title.isEmpty()? title : "Refine Search");

    }

    private void initViews() {
        clearSelection = this.findViewById(R.id.selection_clear);
        recyclerView = (RecyclerView) this.findViewById(R.id.selection_list);
        LinearLayoutManager layoutManager = new LinearLayoutManager(this);
        recyclerView.setLayoutManager(layoutManager);

        clearSelection.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                if(adapter != null){
                    adapter.clearSelections();
                }
            }
        });
    }

    private void setAdapter() {
        List<String> selectableItems = getIntent().getStringArrayListExtra(Constants.DATA);
        String selectedItem = getIntent().getStringExtra(Constants.SELECTED_ITEM);
        adapter = new SelectableRecyclerViewAdapter(this, selectableItems, false,selectedItem);
        recyclerView.setAdapter(adapter);
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        MenuInflater inflater = getMenuInflater();
        inflater.inflate(R.menu.action_done, menu);
        return super.onCreateOptionsMenu(menu);
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        switch (item.getItemId()) {
            case R.id.action_done:
                setResult(RESULT_OK,getResultIntent());
                finish();
                break;

        }
        return super.onOptionsItemSelected(item);
    }

    private Intent getResultIntent() {
        Intent intent = new Intent();
        ArrayList<String> selectedItems = adapter.getSelectedItems();
        intent.putStringArrayListExtra(Constants.DATA,selectedItems);
        return intent;
    }

    @Override
    public void onItemSelected(SelectableItem selectableItem) {
      //  setResult(RESULT_OK,getResultIntent());
      //  finish();
    }

    @Override
    public void onBackPressed() {
        setResult(RESULT_OK,getResultIntent());
        finish();
    }
}
