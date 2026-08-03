// pre request
pm.request.addHeader("x-external-secret: sad");
// pm.request.addHeader('content-type: multipart/form-data');
pm.request.addHeader("accept: application/json");

// post request
const body = pm?.response?.json();
const data = body?.data ?? null;
if (data) {
    if (data.token) {
        pm?.collectionVariables?.set("token", data?.token?.token);
    }
}
