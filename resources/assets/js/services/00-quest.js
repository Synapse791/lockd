class $q
{
    static get(url, data) {
        return $q.request('GET', url, data);
    }

    static put(url, data) {
        return $q.request('PUT', url, data);
    }

    static post(url, data) {
        return $q.request('POST', url, data);
    }

    static patch(url, data) {
        return $q.request('PATCH', url, data);
    }

    static delete(url, data) {
        return $q.request('DELETE', url, data);
    }

    static request(method, url, data) {
        return jQuery.ajax({
                method: method,
                url: url,
                data: data,
                dataType: 'json'
            })
            .fail(response => {
                if (typeof response.responseJSON !== 'undefined') {
                    let msg = typeof response.responseJSON.errorDescription === 'object'
                        ? response.responseJSON.errorDescription.join('<br>')
                        : response.responseJSON.errorDescription;

                    if (response.status === 400)
                        toastr.warning(msg, response.responseJSON.error.toUpperCase());
                    else
                        toastr.error(msg, response.responseJSON.error.toUpperCase());
                } else
                    toastr.error(`Received a non-JSON ${response.status} response from the server.`, 'Something went wrong!');
            });
    }
}