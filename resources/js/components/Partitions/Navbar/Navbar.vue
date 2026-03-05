<template>
  <nav v-if="this.$route.name !== 'dashboard'" class="navbar navbar-expand-lg fixed-top navbar-dark bg-dark"
    id="rbNavbar">
    <router-link id="brand-name" class="navbar-brand mr-4" to="/" active-class="active" exact="">E-Store</router-link>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
      aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"
        @click="changeValue = !changeValue" :class="{'change': changeValue}">
      <!-- <span class="navbar-toggler-icon"></span> -->
      <div class="bg-light line1"></div>
      <div class="bg-light line2"></div>
      <div class="bg-light line3"></div>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
      <ul class="navbar-nav">
        <section id="auth-section" v-if="authenticated === false || authenticated === 'false'">
          <li class="nav-item mx-1">
            <router-link class="nav-link" to="/login" active-class="active">LOGIN</router-link>
          </li>
          <li class="nav-item ml-1 mx-md-3" id="register-btn">
            <router-link class="nav-link buttonX" to="/register" active-class="active">
              <FontAwesomeIcon icon="plus-square" /><span class="ml-1">REGISTER</span>
            </router-link>
          </li>
        </section>
        <li class="nav-item dropdown mx-3" v-if="authenticated === true || authenticated === 'true'">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
            data-toggle="dropdown" aria-haspopup="true"
              aria-expanded="false">{{ user.name }}</a>

          <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <a class="dropdown-item" href="#">Profile</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="#" @click.prevent="signOut">Sign Out</a>
          </div>
        </li>
        <li class="nav-item">
          <router-link class="nav-link" to="/cart" active-class="active">
            <article><FontAwesomeIcon :icon="faCart" />
              <span class="badge badge-pill badge-danger">{{ cartTotalQty || '' }}</span>
            </article>
          </router-link>
        </li>
        <li v-if="this.$route.name !== 'home'" class="nav-item">
          <router-link class="nav-link" to="/" active-class="active">STORE</router-link>
        </li>
        <li v-if="this.$route.name === 'home'" class="nav-item" @click.prevent="inNavigation">
          <a class="nav-link" href="#products-section" active-class="active">PRODUCTS</a>
        </li>
        <li v-if="this.$route.name === 'home'" class="nav-item" @click.prevent="inNavigation">
          <a class="nav-link" href="#viewed-products" active-class="active">VIEWED</a>
        </li>
        <li v-if="this.$route.name === 'home'" class="nav-item" @click.prevent="inNavigation">
          <a class="nav-link" href="#contactus" active-class="active">CONTACT</a>
        </li>
        <li v-if="this.$route.name === 'home'" class="nav-item" @click.prevent="inNavigation">
          <a class="nav-link" href="#aboutus" active-class="active">ABOUT</a>
        </li>
        <li v-if="this.$route.name === 'home'" class="nav-item">
          <router-link class="nav-link" to="/dashboard" active-class="active">ADMIN</router-link>
        </li>
      </ul>
    </div>
  </nav>
</template>

<script>
import { mapGetters, mapActions, mapState } from 'vuex';
import { faShoppingCart } from '@fortawesome/free-solid-svg-icons';

export default {
  data() {
    return {
      faCart: faShoppingCart,
      changeValue: false,
    };
  },

  computed: {
    ...mapState('auth', {
      // token: state => state.token,
      // token: 'token',
    }),
    ...mapState('products', {
      open: state => state.products
    }),
    ...mapState('cart', {
      cartTotalQty: state => state.cart ? state.cart.total_qty : ''
    }),
    ...mapGetters({
      authenticated: 'auth/authenticated',
      user: 'auth/user'
    })
  },

  methods: {
    ...mapActions({
      signOutAction: 'auth/signOut',
      fetchAllProductsAction: 'products/retriveProducts'
    }),
    signOut() {
      this.signOutAction().then(() => {
        // this.$router.replace('/');
      })
      .catch(error => {
        //
      });
    },
    inNavigation(event) {
      // event.preventDefault();
      const targetElement = event.target.getAttribute('href');
      const targetPosition = document.querySelector(targetElement).offsetTop - 200;

      window.scroll({ top: targetPosition, behavior: 'smooth' });
    }
  },

  mounted() {

  }
}
</script>

<style lang="scss" scoped>
@import url('https://fonts.googleapis.com/css2?family=Oswald:wght@200;300;400;500&display=swap');

#rbNavbar {
  background-color: rgba(35, 47, 62, 0.60) !important;
  font-size: 14px;
  font-weight: 400;
  font-family: 'Oswald', sans-serif;

  // #brand-name {
  //   font-family: 'Tangerine', cursive;
  // }

  #auth-section {
    display: flex;
    align-items: center;
  }

  // Menu toggler transformation
  .line1, .line2, .line3 {
    width: 27px;
    height: 2px;
    margin: 5px;
    transition: all 0.4s;
  }

  .change {

    .line1 {
      transform: rotate(-45deg) translate(-5px, 6px);
    }

    .line2 {
      opacity: 0;
    }

    .line3 {
      transform: rotate(45deg) translate(-5px, -6px);
    }
  }

  .navbar-nav {
    .nav-item {
      a {
        transition: all 0.4s linear;

        &.active {
          color: rgb(255, 255, 255);
        }

        &:hover {
          color: rgb(255, 255, 255);
        }
      }
      article {
        position: relative;
        width: 100%;
        // height: 100%;
        margin-right: 1rem;

        svg {
          width: 22px;
          height: 22px;
          // width: 130%;
          // height: 130%;
        }

        span {
          position: absolute;
          top: 0;
          right: 2px;
          // right: 2rem;
        }
      }
    }
  }
}

@media (min-width: 992px) {
  // #navbarSupportedContent {

  // }
}

$color: #0cf;
.buttonX {
  display: inline-block;
  padding: .4rem 1.50rem !important;
  border-radius: 0.3rem;
  color: #000 !important;
  // font-size: 1rem;
  letter-spacing: .15rem;
  transition: all .3s;
  position: relative;
  overflow: hidden;
  z-index: 1;

  &:after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: $color;
    border-radius: 0.3rem;
    z-index: -2;
  }
  &:before {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 0%;
    height: 100%;
    background-color: darken($color, 15%);
    transition: all .5s;
    border-radius: 0.3rem;
    z-index: -1;
  }
  &:hover {
    color: #fff !important;

    &:before {
      width: 100%;
    }
  }
}

</style>
