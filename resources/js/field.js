import IndexField from "./components/IndexField.vue";
import DetailField from "./components/DetailField.vue";
import FormField from "./components/FormField.vue";

Nova.booting((Vue) => {
    Vue.component("index-advanced-image", IndexField);
    Vue.component("detail-advanced-image", DetailField);
    Vue.component("form-advanced-image", FormField);
});
