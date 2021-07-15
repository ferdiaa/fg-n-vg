package com.jasaferdi.fotovideograp.Retrofit;

import com.jasaferdi.fotovideograp.Model.ApiResponse;
import com.jasaferdi.fotovideograp.Model.Appointment;
import com.jasaferdi.fotovideograp.Model.AppointmentSlot;
import com.jasaferdi.fotovideograp.Model.BookingRequest;
import com.jasaferdi.fotovideograp.Model.BookingResponse;
import com.jasaferdi.fotovideograp.Model.ConfirmAppointment;
import com.jasaferdi.fotovideograp.Model.Countries;
import com.jasaferdi.fotovideograp.Model.JobItem;
import com.jasaferdi.fotovideograp.Model.Login.User;
import com.jasaferdi.fotovideograp.Model.LoginData;
import com.jasaferdi.fotovideograp.Model.ManageAppointmentRequest;
import com.jasaferdi.fotovideograp.Model.ManagePrivacyRequest;
import com.jasaferdi.fotovideograp.Model.ManageServicesRequestParam;
import com.jasaferdi.fotovideograp.Model.Provider.BusinessHours;
import com.jasaferdi.fotovideograp.Model.Provider.MarkFavoriteParam;
import com.jasaferdi.fotovideograp.Model.Provider.PrivacySettings;
import com.jasaferdi.fotovideograp.Model.Provider.ProfileServices;
import com.jasaferdi.fotovideograp.Model.Provider.ProviderModel;
import com.jasaferdi.fotovideograp.Model.Provider.ProviderReviewListData;
import com.jasaferdi.fotovideograp.Model.RegisterBusiness;
import com.jasaferdi.fotovideograp.Model.RequestSlots;
import com.jasaferdi.fotovideograp.Model.ResetPassword;
import com.jasaferdi.fotovideograp.Model.ReviewProvider;
import com.jasaferdi.fotovideograp.Model.TokenRequestParam;
import com.jasaferdi.fotovideograp.Model.categories.Category;

import java.util.List;

import retrofit2.Call;
import retrofit2.http.Body;
import retrofit2.http.GET;
import retrofit2.http.POST;
import retrofit2.http.Query;

/**
 * Created by Ferdi on 11/11/2018.
 */

public interface ProviderApi {

        String BASE_SITE = "http://buzsteriphone.com/ferdi/";

        String BASE_URL = BASE_SITE + "wp-json/api/v1/";

        @GET("configs/categories")
        Call<List<Category>> loadCategories();

        @GET("configs/countries")
        Call<List<Countries>> loadCountries();

        @GET("providers")
        Call<List<ProviderModel>> searchProvider(@Query("user_id") int i,
                                                 @Query("category_id") int i2,
                                                 @Query("keyword") String str,
                                                 @Query("geo") String str2,
                                                 @Query("geo_distance") String str3,
                                                 @Query("country") String str4,
                                                 @Query("city") String str5,
                                                 @Query("zip") String str6,
                                                 @Query("lat") String str7,
                                                 @Query("long") String str8);

        @GET("providers")
        Call<List<ProviderModel>> searchProvider(@Query("user_id") int i, @Query("keyword") String str, @Query("geo") String str2, @Query("geo_distance") String str3, @Query("country") String str4, @Query("city") String str5, @Query("zip") String str6, @Query("lat") String str7, @Query("long") String str8);

        @GET("providers")
        Call<List<ProviderModel>> searchProvider(@Query("user_id") int userid,
                                                 @Query("category") String category,
                                                 @Query("keyword") String keyword,
                                                 @Query("geo") String geo,
                                                 @Query("geo_distance") String geo_distance,
                                                 @Query("country") String country,
                                                 @Query("city") String city,
                                                 @Query("zip") String zip,
                                                 @Query("lat") String lat,
                                                 @Query("long") String longitude);

        @GET("providers")
        Call<List<ProviderModel>> getFeaturedProviders(@Query("user_id") int userid, @Query("featured") String featured);

        @POST("providers")
        Call<User> registerUser(@Body RegisterBusiness post);

        @POST("user/login")
        Call<User> loginUser(@Body LoginData post);

        @POST("providers/appointment")
        Call<BookingResponse> makeAppointment(@Body BookingRequest post);

        @POST("providers/confirm-appointment")
        Call<ApiResponse> confirmAppointment(@Body ConfirmAppointment post);

        @GET("user/appointments")
        Call<List<Appointment>> getUserAppointments(@Query("user_id") long userid);

        @POST("user/reset-password")
        Call<ApiResponse> recoverPassword(@Body ResetPassword post);

        @POST("providers/appointment/slots")
        Call<List<AppointmentSlot>> getSlotsOfDate(@Body RequestSlots post);

        @POST("providers/save-rating")
        Call<ApiResponse> saveProviderRating(@Body ReviewProvider rating);

        @POST("user/token")
        Call<ApiResponse> saveUserToken(@Body TokenRequestParam rating);

        @GET("user/services")
        Call<List<ProfileServices>> getUserServices(@Query("user_id") long userId);

        @POST("user/services")
        Call<ApiResponse> updateUserServices(@Body ManageServicesRequestParam params);

        @POST("providers/reviews")
        Call<List<ProviderReviewListData>> getProviderReviews(@Body ReviewProvider params);

        @GET("user/favorites")
        Call<List<ProviderModel>> getUserFavorites(@Query("user_id") long userId);

        @POST("user/favorites")
        Call<ApiResponse> updateUserFavorites(@Body MarkFavoriteParam params);

        @GET("providers/manage-appointments")
        Call<List<Appointment>> getProviderAppointments(@Query("provider_id") long providerId);

        @POST("providers/appointments-status")
        Call<ApiResponse> updateProviderAppointments(@Body ManageAppointmentRequest params);

        @POST("providers/appointments-status2")
        Call<ApiResponse> updateUserAppointments(@Body ManageAppointmentRequest params);

        @GET("providers/privacy-settings")
        Call<PrivacySettings> getPrivacySettings(@Query("publisher_id") long providerId);

        @POST("providers/privacy-settings")
        Call<ApiResponse> updatePrivacySettings(@Body ManagePrivacyRequest request);

        @GET("jobs")
        Call<List<JobItem>> getAllJobs();

        @GET("providers/business-hours")
        Call<BusinessHours> getBusinessHour(@Query("publisher_id") long providerId);

        @POST("providers/business-hours")
        Call<ApiResponse> updateBusinessHour(@Body ManagePrivacyRequest request);

}
