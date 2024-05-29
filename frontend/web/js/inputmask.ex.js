Inputmask.extendAliases({
    email_cr: {
        mask: "*{1,64}[.*{1,64}][.*{1,64}][.*{1,63}]@-{1,63}.-{1,63}[.-{1,63}][.-{1,63}]",
        greedy: !1,
        casing: "lower",
        onBeforePaste: function onBeforePaste(pastedValue, opts) {
            return pastedValue = pastedValue.toLowerCase(), pastedValue.replace("mailto:", "");
        },
        definitions: {
            "*": {
                validator: "[0-9\uff11-\uff19A-Za-zА-Яа-я\u0410-\u044f\u0401\u0451\xc0-\xff\xb5!#$%&'*+/=?^_`{|}~-]"
            },
            "-": {
                validator: "[0-9A-Za-zА-Яа-я-]"
            }
        },
        onUnMask: function onUnMask(maskedValue, unmaskedValue, opts) {
            return maskedValue;
        },
        inputmode: "email_cr"
    },
});
