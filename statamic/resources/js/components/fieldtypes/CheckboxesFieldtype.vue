<template>
    <ul class="list-unstyled">
        <li v-for="option in config.options">
            <input type="checkbox"
                   name="{{ name }}[]"
                   :id="name + '-' + $index"
                   :value="option.value"
                   v-model="data"
    		/>
            <label :for="name + '-' + $index">{{ option.text }}</label>
        </li>
    </ul>
</template>

<script>
export default {

    mixins: [Fieldtype],

    data() {
        return {
            autoBindChangeWatcher: false
        };
    },

    ready: function() {
        if (typeof this.config === 'string') {
            this.config = JSON.parse(this.config);
        }

        if ( ! this.data) {
            this.data = this.config.default || [];
        }

        this.bindChangeWatcher();
    },

    methods: {

        focus() {
            document.getElementById(`${this.name}-0`).focus();
        },

        getReplicatorPreviewText() {
            return this.data.map(item => {
                var option = _.findWhere(this.config.options, {value: item});
                return (option) ? option.text : item;
            }).join(', ');
        },

    }
};
</script>
