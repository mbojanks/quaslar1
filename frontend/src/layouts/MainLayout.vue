<template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated>
      <q-toolbar>
        <q-btn
          flat
          dense
          round
          icon="menu"
          aria-label="Menu"
          @click="toggleLeftDrawer"
        />

        <q-toolbar-title>
          {{ t('Quasar App on Laravel API Test App') }}
        </q-toolbar-title>

        <div>
	  Quasar v{{ $q.version }}
	  <q-select
	    v-model="locale"
	    :options="localeOptions"
	    :label="t('App Language')"
	    dense
	    borderless
	    emit-value
	    map-options
	    options-dense
    	    style="min-width: 150px"
  	  />
	</div>
      </q-toolbar>
    </q-header>

    <q-drawer
      v-model="leftDrawerOpen"
      show-if-above
      bordered
    >
      <q-list>
        <q-item-label
          header
        >
         {{ t('Essential Links') }}
        </q-item-label>

        <EssentialLink
          v-for="link in essentialLinks"
          :key="link.title"
          v-bind="link"
        />
      </q-list>
    </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import messages from '../i18n';
import { useQuasar } from 'quasar';
// import languages from 'quasar/lang/index.json';
import EssentialLink, { EssentialLinkProps } from 'components/EssentialLink.vue';

type MessageSchema = typeof messages.enUS;
const { locale, t } = useI18n<[MessageSchema],'en-US'|'sr'>(/*{ useScope: 'global' }*/);

const $q = useQuasar();

const localeOptions = [
  { value: 'en-US', label: 'English' },
  { value: 'sr', label: 'srpski' }
]; // get all available from src/i18n ?

const essentialLinks: EssentialLinkProps[] = [
  {
    title: 'Docs',
    caption: 'quasar.dev',
    icon: 'school',
    link: 'https://quasar.dev'
  },
  {
    title: 'Github',
    caption: 'github.com/quasarframework',
    icon: 'code',
    link: 'https://github.com/quasarframework'
  },
  {
    title: 'Discord Chat Channel',
    caption: 'chat.quasar.dev',
    icon: 'chat',
    link: 'https://chat.quasar.dev'
  },
  {
    title: 'Forum',
    caption: 'forum.quasar.dev',
    icon: 'record_voice_over',
    link: 'https://forum.quasar.dev'
  },
  {
    title: 'Twitter',
    caption: '@quasarframework',
    icon: 'rss_feed',
    link: 'https://twitter.quasar.dev'
  },
  {
    title: 'Facebook',
    caption: '@QuasarFramework',
    icon: 'public',
    link: 'https://facebook.quasar.dev'
  },
  {
    title: 'Quasar Awesome',
    caption: 'Community Quasar projects',
    icon: 'favorite',
    link: 'https://awesome.quasar.dev'
  }
];

const leftDrawerOpen = ref(false)

function toggleLeftDrawer() {
  leftDrawerOpen.value = !leftDrawerOpen.value
}

// for LangSwitcher component
watch(locale, async (newL, oldL) => {
   // dynamic import, so loading on demand only
   import(
     /* webpackInclude: /(de|en-US)\.js$/ */
     '../i18n/quasar/' + newL + '.ts'
   ).then(lang => {
     $q.lang.set(lang.default);
   }).finally(()=> {
      $q.notify({
        message: 'Old: ' + oldL + ' -> New: ' + newL + ' -- Quasar language: ' + $q.lang.isoName + ' (' + $q.lang.nativeName + ')',
        color: 'positive',
        position: 'top'
      });
   });
});
</script>
