import IndexField from '@/components/IndexField'
import DetailField from '@/components/DetailField'
import FormField from '@/components/FormField'

Nova.booting((Vue) => {
    Vue.component('index-advanced-image', IndexField)
    Vue.component('detail-advanced-image', DetailField)
    Vue.component('form-advanced-image', FormField)
})
