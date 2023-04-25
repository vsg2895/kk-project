export default class Pagination {
    static dataForPage(page, data, perPage = 5) {
        if (!page) page = 1;
        var from = (page - 1) * perPage;
        var to = (page * perPage);

        return data.slice(from, to);
    }
}
