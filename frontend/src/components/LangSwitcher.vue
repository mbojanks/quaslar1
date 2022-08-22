<template>
<q-select
    :value="modelValue"
    :options="langOptions"
    :label="label"
    dense
    borderless
    emit-value
    map-options
    options-dense
    style="min-width: 150px"
    @input="updateValue"
  />

</template>
<script setup lang="ts">
import { useQuasar } from 'quasar';
import languages from 'quasar/lang/index.json';
import { ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';

const { locale, t } = useI18n();

const appLanguages = languages.filter(lang =>
  displayLanguages.includes(lang.isoName);
)

const langOptions = appLanguages.map(lang => ({
  label: lang.nativeName, value: lang.isoName
}));

export interface LangSwitcherProps {
   modelValue: String,
   label: String,
   displayLanguages: String[]
}

const props = withDefaults(defineProps<LangSwitcherProps>(), {
  label: '',
  displayLanguages: () => ['en-US', 'sr']
});

const emit = defineEmits(['update:modelValue']);

const updateValue = (event) => {
   emit('update:modelValue', event.value);
}

watch(props.modelValue, val => {
    // dynamic import, so loading on demand only
    import(
    /* webpackInclude: /(de|en-US)\.js$/ */
    '../i18n/quasar/lang/' + val + '.js'
        ).then(lang => {
          $q.lang.set(lang.default)
        })
});
</script>

