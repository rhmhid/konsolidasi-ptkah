(function () {
    if (jQuery && jQuery.fn && jQuery.fn.select2 && jQuery.fn.select2.amd) var e = jQuery.fn.select2.amd;
    return (
        e.define("select2/i18n/id", [], function () {
            return {
                errorLoading: function () {
                    return "Terjadi kesalahan! Hasil tidak dapat dimuat";
                },
                inputTooLong: function (e) {
                    var t = e.input.length - e.maximum,
                        n = "Silakan ketikan maksimal " + t + " karakter";
                    return n;
                    //return t !== 1 && (n += "s"), n;
                },
                inputTooShort: function (e) {
                    var t = e.minimum - e.input.length,
                        n = "Silakan ketikan minimal " + t + " karakter";
                    return n;
                    //return t !== 1 && (n += "s"), n;
                },
                loadingMore: function () {
                    return "Muat lebih banyak";
                },
                maximumSelected: function (e) {
                    var t = "Data maksimal yg dapat dipilih : " + e.maximum;
                    return n;
                    //return e.maximum !== 1 && (t += "s"), t;
                },
                noResults: function () {
                    return "Data tidak ditemukan";
                },
                searching: function () {
                    return "Mencari..";
                },
            };
        }),
        { define: e.define, require: e.require }
    );
})();