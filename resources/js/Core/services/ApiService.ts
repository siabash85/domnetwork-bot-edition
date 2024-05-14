import type { App } from "vue";
import type { AxiosResponse } from "axios";
import axios from "axios";
import VueAxios from "vue-axios";
import JwtService from "@/Core/services/JwtService";


/**
 * @description service to call HTTP request via Axios
 */
class ApiService {
    /**
     * @description property to share vue instance
     */
    public static vueInstance: App;

    /**
     * @description initialize vue axios
     */
    public static init(app: any) {
        console.log("app", app);

        // ApiService.vueInstance = app;
        // ApiService.vueInstance.use(VueAxios, axios);
        // ApiService.vueInstance.axios.defaults.baseURL =
        //   import.meta.env.VITE_APP_API_URL;

        ApiService.vueInstance = app;
        ApiService.vueInstance.axios = axios;
        ApiService.vueInstance.axios.defaults.baseURL = import.meta.env.VITE_APP_API_URL;;
        ApiService.vueInstance.axios.defaults.withCredentials = true;
    }

    /**
     * @description set the default HTTP request headers
     */
    public static setHeader(): void {
        console.log("toekn", JwtService.getToken());

        ApiService.vueInstance.axios.defaults.headers.common[
            "Authorization"
        ] = `Bearer ${JwtService.getToken()}`;
        ApiService.vueInstance.axios.defaults.headers.common["Accept"] =
            "application/json";
    }

    /**
     * @description send the GET HTTP request
     * @param resource: string
     * @param params: AxiosRequestConfig
     * @returns Promise<AxiosResponse>
     */
    public static query(resource: string, params: any): Promise<AxiosResponse> {
        return ApiService.vueInstance.axios.get(resource, params);
    }

    /**
     * @description send the GET HTTP request
     * @param resource: string
     * @param slug: string
     * @returns Promise<AxiosResponse>
     */
    public static get(
        resource: string,
        slug = "" as string
    ): Promise<AxiosResponse> {
        if (resource.endsWith('/')) {
            resource = resource.slice(0, -1);
        }
        return ApiService.vueInstance.axios.get(`${resource}/${slug}`);
    }

    /**
     * @description set the POST HTTP request
     * @param resource: string
     * @param params: AxiosRequestConfig
     * @returns Promise<AxiosResponse>
     */
    public static post(resource: string, params: any): Promise<AxiosResponse> {
        if (resource.endsWith('/')) {
            resource = resource.slice(0, -1);
        }
        return ApiService.vueInstance.axios.post(`${resource}`, params);
    }
    /**
     * @description send the UPDATE HTTP request
     * @param resource: string
     * @param slug: string
     * @param params: AxiosRequestConfig
     * @returns Promise<AxiosResponse>
     */
    public static update(
        resource: string,
        slug: string,
        params: any
    ): Promise<AxiosResponse> {
        return ApiService.vueInstance.axios.put(`${resource}/${slug}`, params);
    }

    /**
     * @description Send the PUT HTTP request
     * @param resource: string
     * @param params: AxiosRequestConfig
     * @returns Promise<AxiosResponse>
     */
    public static put(
        resource: string,
        params: any,
    ): Promise<AxiosResponse> {
        return ApiService.vueInstance.axios.post(`${resource}`, params, {
            params: {
                "_method": "PUT"
            },
        });
    }

    /**
     * @description Send the DELETE HTTP request
     * @param resource: string
     * @returns Promise<AxiosResponse>
     */
    public static delete(resource: string): Promise<AxiosResponse> {
        return ApiService.vueInstance.axios.delete(resource);
    }
}

export default ApiService;
