import { ref } from "vue";
import { defineStore } from "pinia";
import ApiService from "@/Core/services/ApiService";
import JwtService from "@/Core/services/JwtService";

export interface User {
    email: string;
    password: string;
    // api_token: string;
}

export const useAuthStore = defineStore("auth", () => {
    const errors = ref({});
    const user = ref<User>({} as User);
    const isAuthenticated = ref(!!JwtService.getToken());

    function setAuth(payload) {
        isAuthenticated.value = true;
        user.value = payload.user;
        errors.value = {};
        JwtService.saveToken(payload?.token);
    }
    function setUser(payload) {
        user.value = payload;
    }

    function setError(error: any) {
        errors.value = { ...error };
    }

    function purgeAuth() {
        isAuthenticated.value = false;
        user.value = {};
        errors.value = [];
        JwtService.destroyToken();
    }

    function login(credentials: any) {
        return ApiService.post("/api/auth/login", credentials)
            .then(({ data }) => {
                setAuth(data);
                return data
            })
            .catch(({ response }) => {
                setError(response.data.errors);
                return response
            });
    }

    function logout() {
        return ApiService.post("/api/auth/logout")
            .then(({ data }) => {
                purgeAuth();
                return data
            })
            .catch(({ response }) => {
                setError(response.data.errors);
                return response
            });

    }

    function register(credentials: User) {
        return ApiService.post("register", credentials)
            .then(({ data }) => {
                setAuth(data);
            })
            .catch(({ response }) => {
                setError(response.data.errors);
            });
    }

    function forgotPassword(email: string) {
        return ApiService.post("forgot_password", email)
            .then(() => {
                setError({});
            })
            .catch(({ response }) => {
                setError(response.data.errors);
            });
    }

    function verifyAuth() {
        if (JwtService.getToken()) {
            ApiService.setHeader();
            ApiService.get("/api/user")
                .then(({ data }) => {
                    setUser(data);
                })
                .catch(({ response }) => {
                    setError(response.data.errors);
                    purgeAuth();
                });
        } else {
            purgeAuth();
        }
    }

    return {
        errors,
        user,
        isAuthenticated,
        login,
        logout,
        register,
        forgotPassword,
        verifyAuth,
        setUser
    };
});
