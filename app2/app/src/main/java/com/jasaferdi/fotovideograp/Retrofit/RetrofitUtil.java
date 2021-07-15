package com.jasaferdi.fotovideograp.Retrofit;

import android.text.Html;
import android.util.Log;

import com.google.gson.Gson;
import com.google.gson.GsonBuilder;
import com.jasaferdi.fotovideograp.BuildConfig;
import com.jasaferdi.fotovideograp.Interface.OnDataLoadListener;
import com.jasaferdi.fotovideograp.Interface.OnSignupLoginListener;
import com.jasaferdi.fotovideograp.Model.ApiResponse;
import com.jasaferdi.fotovideograp.Model.Appointment;
import com.jasaferdi.fotovideograp.Model.BookingResponse;
import com.jasaferdi.fotovideograp.Model.Countries;
import com.jasaferdi.fotovideograp.Model.JobItem;
import com.jasaferdi.fotovideograp.Model.Login.User;
import com.jasaferdi.fotovideograp.Model.Provider.BusinessHours;
import com.jasaferdi.fotovideograp.Model.Provider.PrivacySettings;
import com.jasaferdi.fotovideograp.Model.Provider.ProfileServices;
import com.jasaferdi.fotovideograp.Model.Provider.ProviderModel;
import com.jasaferdi.fotovideograp.Model.Provider.ProviderReviewListData;
import com.jasaferdi.fotovideograp.Model.categories.Category;
import com.jasaferdi.fotovideograp.Utils.Constants;
import com.jasaferdi.fotovideograp.Utils.DatabaseUtil;

import java.util.ArrayList;
import java.util.List;
import java.util.concurrent.TimeUnit;

import okhttp3.OkHttpClient;
import okhttp3.logging.HttpLoggingInterceptor;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

/**
 * Created by on 11/29/2017.
 */

public class RetrofitUtil {

    public static ProviderApi createProviderAPI() {

        OkHttpClient.Builder builder = new OkHttpClient().newBuilder();
        builder.readTimeout(60, TimeUnit.SECONDS);
        builder.connectTimeout(60, TimeUnit.SECONDS);

        if (BuildConfig.DEBUG) {
            HttpLoggingInterceptor interceptor = new HttpLoggingInterceptor();
            interceptor.setLevel(HttpLoggingInterceptor.Level.BODY);
            builder.addInterceptor(interceptor);
        }

        Gson gson = new GsonBuilder()
                .setLenient()
                .create();

        OkHttpClient client = builder.build();

        Retrofit retrofit =
                new Retrofit.Builder().baseUrl(ProviderApi.BASE_URL).client(client).
                        addConverterFactory(GsonConverterFactory.create(gson)).build();

        return retrofit.create(ProviderApi.class);
    }

    public static Callback<List<ProviderModel>> getProviders(final OnDataLoadListener listener) {

        return new Callback<List<ProviderModel>>() {
            @Override
            public void onResponse(Call<List<ProviderModel>> call, Response<List<ProviderModel>> response) {
                if (response.isSuccessful()) {
                    List<ProviderModel> data = new ArrayList<>();
                    if (response.body() != null)
                        data.addAll(response.body());
                    listener.onProviderLoad(data);
                } else {
                    listener.onError(Constants.Errors.PROVIDER,response.errorBody().toString());
                    Log.d("QuestionsCallback", "Code: " + response.code() + " Message: " + response.message());
                }
            }

            @Override
            public void onFailure(Call<List<ProviderModel>> call, Throwable t) {
                t.printStackTrace();
                listener.onError(Constants.Errors.PROVIDER,"terjadi kesalahan, coba lagi!");
            }
        };
    }

    public static Callback<List<Category>> getCategries(final OnDataLoadListener listener) {

        return new Callback<List<Category>>() {
            @Override
            public void onResponse(Call<List<Category>> call, Response<List<Category>> response) {
                if (response.isSuccessful()) {
                    List<Category> data = new ArrayList<>();
                    if (response.body() != null) {
                        data.addAll(response.body());
                        DatabaseUtil.getInstance().delteCategoriesList();
                        DatabaseUtil.getInstance().storeCategoriesList(data);
                    }else{
                        listener.onError(Constants.Errors.CATEGORY_FAILED,"terjadi kesalahan coba lagi");
                    }
                    listener.onCategoriesLoad(data);
                } else {
                    Log.d("QuestionsCallback", "Code: " + response.code() + " Message: " + response.message());
                    listener.onError(Constants.Errors.CATEGORY_FAILED,"terjadi kesalahan coba lagi");
                }
            }

            @Override
            public void onFailure(Call<List<Category>> call, Throwable t) {
                t.printStackTrace();
                listener.onError(Constants.Errors.CATEGORY_FAILED,"terjadi kesalahan coba lagi");
            }
        };

    }

    public static Callback<User> SignupUser(final OnSignupLoginListener listener) {

        return new Callback<User>() {
            @Override
            public void onResponse(Call<User> call, Response<User> response) {
                if (response.isSuccessful()) {
                   listener.onSignup(response.body());
                } else {
                    Log.d("QuestionsCallback", "Code: " + response.code() + " Message: " + response.message());
                    listener.OnError(response.message());
                }
            }

            @Override
            public void onFailure(Call<User> call, Throwable t) {
                t.printStackTrace();
                listener.OnError(t.getLocalizedMessage());
            }
        };

    }


    public static Callback<User> loginUser(final OnSignupLoginListener listener) {

        return new Callback<User>() {
            @Override
            public void onResponse(Call<User> call, Response<User> response) {
                if (response.isSuccessful()) {
                    if (response.body() != null) {
                        listener.onLoginUser(response.body());
                        DatabaseUtil.getInstance().storeUser(response.body());
                    }
                } else {
                        listener.OnError("Email atau password salah!");
                }
            }

            @Override
            public void onFailure(Call<User> call, Throwable t) {
                t.printStackTrace();
                listener.OnError(t.getLocalizedMessage());
            }
        };

    }

    public static Callback<List<Countries>> loadCountries(final OnDataLoadListener listener) {

        return new Callback<List<Countries>>() {
            @Override
            public void onResponse(Call<List<Countries>> call, Response<List<Countries>> response) {
                if (response.isSuccessful()) {
                    if (response.body() != null) {
                        listener.onCountriesLoad(response.body());
                        DatabaseUtil.getInstance().storeCountries(response.body());
                    }
                } else {
                    listener.onError(Constants.Errors.COUNTRY_LOAD_FAILED,"terjadi kesalahan coba lagi!");
                }
            }

            @Override
            public void onFailure(Call<List<Countries>> call, Throwable t) {
                t.printStackTrace();
                listener.onError(Constants.Errors.COUNTRY_LOAD_FAILED,t.getLocalizedMessage());
            }
        };

    }

    public static Callback<BookingResponse> makeAppointement(final OnDataLoadListener listener) {

        return new Callback<BookingResponse>() {
            @Override
            public void onResponse(Call<BookingResponse> call, Response<BookingResponse> response) {
                if (response.isSuccessful()) {
                    if (response.body() != null && response.body().getType().equals("success")) {
                        listener.onSuccess("Appointement has been made.");
                    }
                } else {
                    listener.onError(Constants.Errors.APPOINTMENT_FAILED,"terjadi kesalahan coba lagi!");
                }
            }

            @Override
            public void onFailure(Call<BookingResponse> call, Throwable t) {
                t.printStackTrace();
                listener.onError(Constants.Errors.APPOINTMENT_FAILED,t.getLocalizedMessage());
            }
        };

    }

    public static Callback<ApiResponse> confirmAppointement(final OnDataLoadListener listener) {

        return new Callback<ApiResponse>() {
            @Override
            public void onResponse(Call<ApiResponse> call, Response<ApiResponse> response) {
                if (response.isSuccessful()) {
                    if (response.body() != null && response.body().getType().equals("success")) {
                        listener.onSuccess(Html.fromHtml(response.body().getApptData()).toString());
                    }
                } else {
                    listener.onError(Constants.Errors.APPOINTMENT_FAILED,"terjadi kesalahan coba lagi!");
                }
            }

            @Override
            public void onFailure(Call<ApiResponse> call, Throwable t) {
                t.printStackTrace();
                listener.onError(Constants.Errors.APPOINTMENT_FAILED,t.getLocalizedMessage());
            }
        };

    }

    public static Callback<ApiResponse> recoverPassword(final OnDataLoadListener listener) {

        return new Callback<ApiResponse>() {
            @Override
            public void onResponse(Call<ApiResponse> call, Response<ApiResponse> response) {
                if (response.isSuccessful()) {
                    if (response.body() != null){
                        if(response.body().getType().equals(Constants.SUCCESS)) {
                            listener.onSuccess(Html.fromHtml(response.body().getMessage()).toString());
                        }else{
                            listener.onError(Constants.Errors.FAILED,response.body().getMessage());
                        }
                    }
                } else {
                    listener.onError(Constants.Errors.FAILED,"terjadi kesalahan coba lagi!");
                }
            }

            @Override
            public void onFailure(Call<ApiResponse> call, Throwable t) {
                t.printStackTrace();
                listener.onError(Constants.Errors.FAILED,t.getLocalizedMessage());
            }
        };

    }

    public static Callback<List<Appointment>> getUserAppointments(final OnDataLoadListener listener) {

        return new Callback<List<Appointment>>() {
            @Override
            public void onResponse(Call<List<Appointment>> call, Response<List<Appointment>> response) {
                if (response.isSuccessful()) {
                    if (response.body() != null) {
                        listener.onAppointmentsLoad(response.body());
                    }
                } else {
                    listener.onError(Constants.Errors.COUNTRY_LOAD_FAILED,"terjadi kesalahan coba lagi!");
                }
            }

            @Override
            public void onFailure(Call<List<Appointment>> call, Throwable t) {
                t.printStackTrace();
                listener.onError(Constants.Errors.COUNTRY_LOAD_FAILED,t.getLocalizedMessage());
            }
        };

    }

    public static Callback<ApiResponse> sendRequest(final OnDataLoadListener listener) {

        return new Callback<ApiResponse>() {
            @Override
            public void onResponse(Call<ApiResponse> call, Response<ApiResponse> response) {
                if(listener != null) {
                    if (response.isSuccessful()) {
                        if (response.body() != null) {
                            if (response.body().getType()!= null && response.body().getType().equals(Constants.SUCCESS)) {
                                listener.onSuccess(Html.fromHtml(response.body().getMessage()).toString());
                            } else {
                                listener.onError(Constants.Errors.FAILED, response.body().getMessage());
                            }
                        }
                    } else {
                        listener.onError(Constants.Errors.FAILED, "terjadi kesalahan coba lagi!");
                    }
                }
            }

            @Override
            public void onFailure(Call<ApiResponse> call, Throwable t) {
                t.printStackTrace();
                if(listener != null) {
                    listener.onError(Constants.Errors.FAILED, t.getLocalizedMessage());
                }
            }
        };

    }

    public static Callback<List<ProfileServices>> getProviderServices(final OnDataLoadListener listener) {

        return new Callback<List<ProfileServices>>() {
            @Override
            public void onResponse(Call<List<ProfileServices>> call, Response<List<ProfileServices>> response) {
                if (response.isSuccessful()) {
                    if (response.body() != null) {
                        listener.onServiceLoad(response.body());
                    }
                } else {
                    listener.onError(Constants.Errors.FAILED,"terjadi kesalahan coba lagi!");
                }
            }

            @Override
            public void onFailure(Call<List<ProfileServices>> call, Throwable t) {
                t.printStackTrace();
                listener.onError(Constants.Errors.FAILED,t.getLocalizedMessage());
            }
        };

    }


    public static Callback<List<ProviderReviewListData>> getProviderReviews(final OnDataLoadListener listener) {

        return new Callback<List<ProviderReviewListData>>() {
            @Override
            public void onResponse(Call<List<ProviderReviewListData>> call, Response<List<ProviderReviewListData>> response) {
                if (response.isSuccessful()) {
                    if (response.body() != null) {
                        listener.onReviewsLoad(response.body());
                    }
                } else {
                    listener.onError(Constants.Errors.FAILED,"terjadi kesalahan coba lagi!");
                }
            }

            @Override
            public void onFailure(Call<List<ProviderReviewListData>> call, Throwable t) {
                t.printStackTrace();
                listener.onError(Constants.Errors.FAILED,t.getLocalizedMessage());
            }
        };

    }

    public static Callback<ApiResponse> updateUserFavorites(final ProviderModel item,final OnDataLoadListener listener) {

        return new Callback<ApiResponse>() {
            @Override
            public void onResponse(Call<ApiResponse> call, Response<ApiResponse> response) {
                if(listener != null) {
                    if (response.isSuccessful()) {
                        if (response.body() != null) {
                            if (response.body().getType()!= null && response.body().getType().equals(Constants.SUCCESS)) {
                                listener.onUpdateFavorites(item);
                            } else {
                                listener.onError(Constants.Errors.FAILED, response.body().getMessage());
                            }
                        }
                    } else {
                        listener.onError(Constants.Errors.FAILED, "terjadi kesalahan coba lagi!");
                    }
                }
            }

            @Override
            public void onFailure(Call<ApiResponse> call, Throwable t) {
                t.printStackTrace();
                if(listener != null) {
                    listener.onError(Constants.Errors.FAILED, t.getLocalizedMessage());
                }
            }
        };

    }

    public static Callback<PrivacySettings> getPrivacySettings(final OnDataLoadListener listener) {

        return new Callback<PrivacySettings>() {
            @Override
            public void onResponse(Call<PrivacySettings> call, Response<PrivacySettings> response) {
                if (response.isSuccessful()) {
                    if (response.body() != null) {
                        listener.onPrivacyLoaded(response.body());
                    }
                } else {
                    listener.onError(Constants.Errors.FAILED,"terjadi kesalahan coba lagi!");
                }
            }

            @Override
            public void onFailure(Call<PrivacySettings> call, Throwable t) {
                t.printStackTrace();
                listener.onError(Constants.Errors.FAILED,t.getLocalizedMessage());
            }
        };

    }
    public static Callback<BusinessHours> getBusinessHours(final OnDataLoadListener listener) {

        return new Callback<BusinessHours>() {
            @Override
            public void onResponse(Call<BusinessHours> call, Response<BusinessHours> response) {
                if (response.isSuccessful()) {
                    if (response.body() != null) {
                        listener.onBusinessHoursLoaded(response.body());
                    }
                } else {
                    listener.onError(Constants.Errors.FAILED,"terjadi kesalahan coba lagi!");
                }
            }

            @Override
            public void onFailure(Call<BusinessHours> call, Throwable t) {
                t.printStackTrace();
                listener.onError(Constants.Errors.FAILED,t.getLocalizedMessage());
            }
        };

    }

    public static Callback<List<JobItem>> getAllJobs(final OnDataLoadListener listener) {

        return new Callback<List<JobItem>>() {
            @Override
            public void onResponse(Call<List<JobItem>> call, Response<List<JobItem>> response) {
                if (response.isSuccessful()) {
                    if (response.body() != null) {
                        listener.onJobsLoaded(response.body());
                    }
                } else {
                    listener.onError(Constants.Errors.FAILED,"terjadi kesalahan coba lagi!");
                }
            }

            @Override
            public void onFailure(Call<List<JobItem>> call, Throwable t) {
                t.printStackTrace();
                listener.onError(Constants.Errors.FAILED,t.getLocalizedMessage());
            }
        };

    }

}
