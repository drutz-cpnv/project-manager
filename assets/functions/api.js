export async function jsonFetch (
    url,
    options = {
        headers: {
            Accept: 'application/json',
        },
    },
) {
    const response = await fetch(url, options)
    if (response.status === 204) {
        return null;
    }
    if (response.ok) {
        return await response.json()
    }
    throw response
}