<template>
  <div class="login-screen">
    <!-- Logo + tagline -->
    <div class="login-hero">
      <img src="../assets/logo-full.jpeg" alt="ShopTogether" class="login-logo" />
      <p class="login-sub">Tu lista de compras colaborativa</p>
    </div>

    <!-- Formulario -->
    <div class="login-card">
      <Transition name="fade" mode="out-in">

        <!-- ── LOGIN ── -->
        <div v-if="mode === 'login'" key="login">
          <div class="field">
            <label class="inp-label">Correo electrónico</label>
            <input v-model="modalLogin.email" class="inp" type="email" placeholder="tu@email.com" />
          </div>
          <div class="field" style="margin-bottom:20px;">
            <label class="inp-label">Contraseña</label>
            <input v-model="modalLogin.password" class="inp" type="password" placeholder="••••••••" />
          </div>
          <button class="btn btn-primary" @click="DoLoging" :disabled="!canSubmit">
            Iniciar sesión
          </button>
          <p class="switch-mode">
            ¿No tienes cuenta?
            <span class="switch-link" @click="mode = 'register'">Crear cuenta</span>
          </p>
          <!-- Divider -->
          <div class="divider">
            <div class="div-line" /><span class="div-text">o continúa con</span><div class="div-line" />
          </div>
          <!-- Google -->
          <button id="google-btn" class="btn-google">
            <svg width="18" height="18" viewBox="0 0 24 24">
              <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
              <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
              <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
              <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            Continuar con Google
          </button>
        </div>

        <!-- ── REGISTRO ── -->
        <div v-else key="register">
          <div class="field">
            <label class="inp-label">Nombre</label>
            <input v-model="modalCreateUser.name" class="inp" placeholder="Tu nombre" />
          </div>
          <div class="field">
            <label class="inp-label">Correo electrónico</label>
            <input v-model="modalCreateUser.email" class="inp" type="email" placeholder="tu@email.com" />
          </div>
          <div class="field" >
            <label class="inp-label">Contraseña</label>
            <input v-model="modalCreateUser.password" class="inp" type="password" placeholder="Mínimo 8 caracteres" />
          </div>
          <div class="field" style="margin-bottom:20px;">
            <label class="inp-label">Repetir Contraseña</label>
            <input v-model="modalCreateUser.confirm_password" class="inp" type="password" placeholder="Mínimo 8 caracteres" />
          </div>
          <button class="btn btn-primary" @click="CreateUser" :disabled="!passwordsMatch">
            Crear cuenta
          </button>
          <p class="switch-mode">
            ¿Ya tienes cuenta?
            <span class="switch-link" @click="mode = 'login'">Iniciar sesión</span>
          </p>
          <!-- Divider -->
          <div class="divider">
            <div class="div-line" /><span class="div-text">o continúa con</span><div class="div-line" />
          </div>
          <!-- Google -->
          <button id="google-btn" class="btn-google">
            <svg width="18" height="18" viewBox="0 0 24 24">
              <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
              <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
              <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
              <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            Continuar con Google
          </button>
        </div>

      </Transition>
    </div>
  </div>
</template>

<script setup>
import { onMounted, onBeforeUnmount, ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import api, { SetAuthToken } from '../service/axiosInstance'
import { SetUser } from '../stores/user.js'
import { Alert } from '../stores/alert.js'

const router = useRouter()
const CLIENT_ID = import.meta.env.VITE_GOOGLE_CLIENT_ID;
const modalLogin = ref({email : '' , password : ''});
const modalCreateUser = ref({name: '', email: '', password: '', confirm_password: '', image: null});
const imagePreview = ref(null);
const mode = ref('login') // 'login' o 'register'
const google = window.google; // Asegura que google esté disponible globalmente

const passwordsMatch = computed(() =>
  modalCreateUser.value.password && modalCreateUser.value.password === modalCreateUser.value.confirm_password
)

const canSubmit = computed(() => modalLogin.value.email.trim() && modalLogin.value.password.length >= 6)

const CreateUser = () => {
  const formData = new FormData()
  formData.append('name', modalCreateUser.value.name || '')
  formData.append('email', modalCreateUser.value.email || '')
  formData.append('password', modalCreateUser.value.password || '')
  formData.append('confirm_password', modalCreateUser.value.confirm_password || '')

  if (modalCreateUser.value.image) {
    formData.append('image', modalCreateUser.value.image)
  }
  api.post('/auth/register', formData, {
    headers: {
      'Content-Type': 'multipart/form-data',
    },
  })
  .then(res => {
    let response = res.data;
    if(response.status_code != 201) return Alert({msg: 'Error al crear usuario', color: 'red'});
    modalCreateUser.value = null;
    Alert({msg: response.message, color: 'green'});
  }).catch(err => {
    console.error('Error al crear usuario:', err)
    Alert({msg: 'Error al crear usuario', color: 'red'});
  })
}

const DoLoging = () => {
  if(!modalLogin.value.email && !modalLogin.value.password) return Alert({msg : 'Ingrese los datos de inicio de sesión',color : 'yellow'});
  if(!modalLogin.value.email) return Alert({msg : 'El correo es requerido',color : 'yellow'});
  if(!modalLogin.value.password) return Alert({msg :'La contraseña es requerida', color :'yellow'});

  api.post('/auth/login', modalLogin.value)
  .then(res => {
    console.log("SI");
    
    let response = res.data;
    SetUser(response.user);
    SetAuthToken(response.token);
    router.push({ name: 'lists' });
  }).catch(err => {
    console.log("NO");
    let msg = err.response?.data?.message ?? 'Error al iniciar sesión';
    Alert?.({msg, color:'red'});
    modalLogin.value = {email : '' , password : ''};
  })
}

function handleImage(e) {
  const file = e.target.files[0]
  if (!file) return
  modalCreateUser.value.image = file
  imagePreview.value = URL.createObjectURL(file)
}

// function InitGoogle() {
//   console.log(CLIENT_ID);
  
//   const el = document.getElementById('google-btn');
//   console.log(el);
  
//   if (!el) return;

//   el.innerHTML = ''; // 🔑 CLAVE para logout/login

//   google.accounts.id.initialize({
//     client_id: CLIENT_ID,
//     callback: HandleCredentialResponse,
//     ux_mode: 'popup',
//   });

//   google.accounts.id.renderButton(el, {
//     theme: 'filled_blue',
//     size: 'large',
//     width: 260,
//     logo_alignment : 'center',
//     locale: 'es',
//     shape : 'pill',
//     text : 'continue_with'
//   });
// }

// onMounted(() => {
//   InitGoogle();
// });

// onBeforeUnmount(() => {
//   if (window.google?.accounts?.id) {
//     google.accounts.id.cancel();
//   }
// });

// function HandleCredentialResponse(response) {
//   api.post('/auth/google', {
//     token: response.credential,
//   })
//   .then(res => {
//     let response = res.data.data;
//     SetUser(response.user);
//     localStorage.setItem('token', response.access_token);
//     window.location.href = '/lists';
//   })
//   .catch(err => {
//     console.error('Error autenticación:', err)
//   })
// }
</script>

<style scoped>
.login-screen {
  min-height: 100vh;
  background: linear-gradient(160deg, #EBF4FF 0%, #F4F6FA 55%);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 40px 24px 32px;
}

.login-hero { text-align: center; margin-bottom: 28px; }
.login-logo { width: 220px; display: block; margin: 0 auto 6px; }
.login-sub  { font-size: 13px; color: #5A6B82; margin: 0; }

.login-card {
  width: 100%;
  max-width: 390px;
  background: white;
  border-radius: 24px;
  padding: 24px;
  box-shadow: 0 8px 40px rgba(21, 101, 192, 0.08);
}

.field { margin-bottom: 14px; }
.inp-label {
  font-size: 12px;
  font-weight: 600;
  color: #5A6B82;
  margin-bottom: 6px;
  display: block;
}
.inp {
  width: 100%;
  border: 1.5px solid #E8ECF2;
  border-radius: 14px;
  padding: 12px 15px;
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 14px;
  color: #1A2332;
  background: #F4F6FA;
  outline: none;
  transition: border-color .15s, box-shadow .15s;
  box-sizing: border-box;
}
.inp:focus { border-color: #1565C0; box-shadow: 0 0 0 3px rgba(21,101,192,.1); background: white; }

.btn {
  width: 100%; padding: 14px; border: none; border-radius: 14px;
  font-family: 'Nunito', sans-serif; font-weight: 800; font-size: 15px;
  cursor: pointer; display: flex; align-items: center; justify-content: center;
}
.btn-primary { background: linear-gradient(135deg,#1565C0,#00ACC1); color: white; box-shadow: 0 4px 16px rgba(21,101,192,.3); }
.btn:disabled { opacity: .5; cursor: not-allowed; }

.switch-mode {
  text-align: center;
  font-size: 13px;
  color: #5A6B82;
  margin: 14px 0 0;
}
.switch-link {
  color: #1565C0;
  font-weight: 600;
  cursor: pointer;
}

.divider {
  display: flex;
  align-items: center;
  gap: 10px;
  margin: 14px 0;
}
.div-line { flex: 1; height: 1px; background: #E8ECF2; }
.div-text  { font-size: 11px; color: #9AABBB; white-space: nowrap; }

.btn-google {
  width: 100%;
  padding: 12px;
  border: 1.5px solid #E8ECF2;
  border-radius: 12px;
  background: white;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  font-weight: 600;
  font-size: 13px;
  color: #1A2332;
  cursor: pointer;
  font-family: 'Plus Jakarta Sans', sans-serif;
  transition: background .15s;
}
.btn-google:hover { background: #F4F6FA; }

.fade-enter-active, .fade-leave-active { transition: opacity .2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
