package com.jasaferdi.fotovideograp.Interface;

import com.jasaferdi.fotovideograp.Model.Login.User;

/**
 * Created by  on 12/24/2017.
 */

public interface OnSignupLoginListener  {
    void onSignup(User data);
    void onLoginUser(User data);
    void OnError(String error);
}
