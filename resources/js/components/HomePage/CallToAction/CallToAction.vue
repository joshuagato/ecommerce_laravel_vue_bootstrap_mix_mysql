<template>
  <header class="moveFromTop">
    <span class="welcome moveIntroFromBottom">Introducing E-Store</span>
    <vue-typed-js :loop="true" :showCursor="true" :typeSpeed="40"
      :strings="['The home of anything you want to buy...',
      'Affordable and rational prices...', '...and many more.']">
      <span class="typing moveTypedFromBottom"></span>
    </vue-typed-js>
  </header>
</template>

<script>
export default {
  mounted() {
    const name = 'Joshua';
    let reversed = '';
    // console.log(name.split('').reverse().join(''));
    for (let character of name) {
      reversed = character + reversed;
    }

    console.log(reversed);
  }
}
</script>

<style lang="scss" scoped>
  header {
    position: relative;
    margin-top: 0;
    padding-top: 0;
    min-height: 75vh;
    width: 100%;
    background: linear-gradient(rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.2)), url(../../backgrounds/mall.jpg) no-repeat scroll center center /cover;
    border-bottom-right-radius: 70% 75px;
    border-bottom-left-radius:  70% 75px;

    .welcome, .typing {
      position: absolute;
      color: #ffffff;
      text-align: center;
      margin-left: auto;
      margin-right: auto;
      left: 0;
      right: 0;
      top: 50%;
      bottom: 50%;
      text-align: center;
      // opacity: 0;
      // visibility: hidden;
    }

    .welcome {
      font-weight: bold;
      font-size: 2.3rem;
      top: 48%;
    }

    .typing {
      font-weight: 500;
      font-size: 1.2rem;
      top: 60%;
    }
  }

  .moveFromTop {
    animation-name: moveFromTop;
    animation-fill-mode: forwards;
    animation-duration: 1s;
  }

  .moveIntroFromBottom {
    animation-name: moveIntroFromBottom;
    animation-fill-mode: forwards;
    animation-delay: 0.5s;
    animation-duration: 0.9s;
    visibility: hidden;
  }

  .moveTypedFromBottom {
    animation-name: moveTypedFromBottom;
    animation-fill-mode: forwards;
    animation-delay: 1s;
    animation-duration: 0.9s;
    visibility: hidden;
  }

  @keyframes moveFromTop {
    0% {
      transform: translateY(-180px);
      opacity: 0;
    }

    100% {
      transform: translateY(0);
      opacity: 1;
      visibility: visible;
    }
  }

  @keyframes moveIntroFromBottom {
    0% {
      transform: translateY(170px);
      opacity: 0;
      visibility: visible;
    }

    100% {
      transform: translateY(0);
      opacity: 1;
      visibility: visible;
    }
  }

  @keyframes moveTypedFromBottom {
    0% {
      transform: translateY(40px);
      opacity: 0;
      visibility: visible;
    }

    100% {
      transform: translateY(0);
      opacity: 1;
      visibility: visible;
    }
  }
</style>
