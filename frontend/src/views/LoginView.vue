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
            <input v-model="modalCreateUser.password_confirmation" class="inp" type="password" placeholder="Mínimo 8 caracteres" />
          </div>
          <button class="btn btn-primary" @click="CreateUser" :disabled="!passwordsMatch">
            Crear cuenta
          </button>
          <p class="switch-mode">
            ¿Ya tienes cuenta?
            <span class="switch-link" @click="mode = 'login'">Iniciar sesión</span>
          </p>
        </div>

      </Transition>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import api, { SetAuthToken } from '../service/axiosInstance'
import { SetUser } from '../stores/user.js'
import { Alert } from '../stores/alert.js'

const router = useRouter()
const modalLogin = ref({ email: '', password: '' })
const modalCreateUser = ref({ name: '', email: '', password: '', password_confirmation: '', image: null })
const imagePreview = ref(null)
const mode = ref('login')

const passwordsMatch = computed(() =>
  modalCreateUser.value.password && modalCreateUser.value.password === modalCreateUser.value.password_confirmation
)

const canSubmit = computed(() => modalLogin.value.email.trim() && modalLogin.value.password.length >= 6)

const CreateUser = () => {
  const formData = new FormData()
  formData.append('name', modalCreateUser.value.name || '')
  formData.append('email', modalCreateUser.value.email || '')
  formData.append('password', modalCreateUser.value.password || '')
  formData.append('password_confirmation', modalCreateUser.value.password_confirmation || '')

  if (modalCreateUser.value.image) {
    formData.append('image', modalCreateUser.value.image)
  }
  api.post('/auth/register', formData, { headers: { 'Content-Type': 'multipart/form-data' } })
  .then(() => {
    Alert({ msg: '¡Cuenta creada! Inicia sesión.', color: 'green' })
    modalCreateUser.value = { name: '', email: '', password: '', password_confirmation: '', image: null }
    mode.value = 'login'
  }).catch(err => {
    const msg = err.response?.data?.message ?? 'Error al crear usuario'
    Alert({ msg, color: 'red' })
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


.fade-enter-active, .fade-leave-active { transition: opacity .2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
