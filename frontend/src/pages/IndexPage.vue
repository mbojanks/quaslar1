<template>
  <q-page class="row items-center justify-evenly">
    <div class="column">
      <div class="row">
        <h5 class="text-h5 text-white q-my-md">BoCo</h5>
      </div>
      <div class="row">
      <!-- <p v-if="loggedUser">{{ loggedUser.first_name  }} {{ loggedUser.last_name }} <q-btn :label="t('Logout')" @click="logoutUser" /></p> -->
       <div v-if="loggedUser">
	<p>{{ $t('welcomeLogged') + calcWelcome.firstname + ' ' + calcWelcome.lastname }}</p><q-btn :label="t('Logout')" @click="logoutUser" />
       </div>
       <q-card v-else square bordered class="q-pa-lg shadow-1">
          <q-card-section>
            <q-form class="q-gutter-md">
              <q-input square filled clearable v-model="email" type="email" :label="t('email')" />
              <q-input square filled clearable v-model="password" type="password" :label="t('password')" />
            </q-form>
          </q-card-section>
          <q-card-actions class="q-px-md">
            <q-btn unelevated color="primary" icon="record-voice-over" size="lg" class="full-width" :label="t('Login')" @click="loginUser" />
          </q-card-actions>
          <q-card-section class="text-center q-pa-none">
            <p class="text-grey-6">{{ t('Not registered? Create an Account') }}</p>
          </q-card-section>
        </q-card>
       </div>
    </div>
    <!--<example-component
      active
      :title="t('exampleComponent')"
      :todos="todos"
      :meta="meta"
    ></example-component>
    <q-btn round color="primary" icon="shopping_cart" @click="loadData">{{ t('Load data') }}</q-btn>
    <p>{{ data }}</p>-->
  </q-page>
</template>

<script setup lang="ts">
//import { Todo, Meta } from 'components/models';
//import ExampleComponent from 'components/ExampleComponent.vue';
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { api } from 'boot/axios';
import { useQuasar } from 'quasar';

const { t } = useI18n(); // have to be first: call to `useI18n`, and spread `t` from  `useI18n` returning

const $q = useQuasar();

let email = ref(null);
let password = ref(null);

let loggedUser = ref<Record<string, unknown>>(null);
const calcWelcome = computed(() => loggedUser.value ? { firstname: loggedUser.value.first_name, lastname: loggedUser.value.last_name  } : null );

function loginUser() {
    api.post('/login', { email: email.value, password: password.value })
      .then((response) => {
        $q.notify({
          color: 'positive',
          position: 'top',
          message: t('Login success'),
          icon: 'announcement'
        });
	loggedUser.value = response.data.user;
	console.log(loggedUser);
	//user from response to pinia state
      }).
      catch((error) => {
	let msg = t('Login failed: ');
	if (error.response) {
          // The request was made and the server responded with a status code
          // that falls out of the range of 2xx
	  // Take error message from server
	    msg = error.response.data.error;
        } else if (error.request) {
          // The request was made but no response was received
          // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
          // http.ClientRequest in node.js
          msg = 'No response for Request: ' + JSON.stringify(error.request);
        } else {
          // Something happened in setting up the request that triggered an Error
          msg = 'Error in setting up the request: ' + JSON.stringify(error.message);
        }
        // msg = JSON.stringify(error.config);
	$q.notify({
          color: 'negative',
          position: 'top',
          message: msg,
          icon: 'report_problem'
        });
      });
}

function logoutUser() {
    api.post('/logout', { })
      .then((response) => {
	$q.notify({
	  color: 'positive',
	  position: 'top',
	  message: response.data.message,
	  icon: 'announcement'
	});
	loggedUser.value = null;
      }).
      catch((err) => {
	$q.notify({
	  color: 'negative',
	  position: 'top',
	  message: t('Logout failed due to bad request: ') + err.toString(),
	  icon: 'report_problem'
	});
      });
}

</script>
