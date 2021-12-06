<div class="swte-limited-time-deal">
      <a href="https://swiftperformance.io/upgrade-pro-limited-time-deal/" target="_blank">
            <h4>Limited time deal</h4>
            <img src="<?php echo SWIFT_PERFORMANCE_URI?>/templates/ads/images/upgrade-promo.png" style="width:100%;">
            <span class="swift-btn swift-btn-green">UPGRADE NOW</span>
            <div class="swte-flipclock" id="flipclock-1"></div>
      </a>
</div>

<style>

@font-face {
      font-family: protestpaint;
      src: url(<?php echo SWIFT_PERFORMANCE_URI?>/templates/ads/assets/protestpaint-bb.regular.woff);
}

.swte-limited-time-deal a {
      text-decoration: none;
}

.swte-limited-time-deal {
      text-align: center;
}

.swte-limited-time-deal h4 {
      font-family: protestpaint;
      font-size: 4em !important;
      text-align: center;
      color: #ee2a1c !important;
      text-transform: uppercase;
}

.swte-limited-time-deal .swift-btn {
      margin-top: 30px;
      font-size: 1.75em;
}

.swte-flipclock {
  display: flex;
  justify-content: center;
  padding: 40px;
  box-sizing: border-box;
}

.swte-flipclock * {
  box-sizing: inherit;
}

.swte-leaf {
  display: flex;
  flex-direction: column;
  margin: 0 4px;
  perspective: 300px;
}

.swte-leaf::after {
  content: attr(data-label);
  position: absolute;
  top: 100%;
  left: 50%;
  margin-top: 8px;
  transform: translateX(-50%);
  color: #ec1f22;
  font-size: 14px;
  font-weight: 400;
  text-transform: uppercase;
  opacity: 0.5;
}

.swte-flip-top,
.swte-flip-bottom,
.swte-leaf-front,
.swte-leaf-back {
  position: relative;
  display: block;
  height: 64px;
  width: 120px;
  background-color: #ec1f22;
  color: #fff;
  overflow: hidden;
  border-color: #ec1f22;
}
.swte-flip-top span,
.swte-flip-bottom span,
.swte-leaf-front span,
.swte-leaf-back span {
  position: absolute;
  left: 50%;
  width: 100%;
  height: 128px;
  text-align: center;
  font-family: "Impact", sans serif;
  font-size: 80px;
  line-height: 128px;
  transform: translateX(-50%);
}

.swte-flip-leaf._3-digits .swte-flip-top,
.swte-flip-leaf._3-digits .swte-flip-bottom,
.swte-flip-leaf._3-digits .swte-leaf-front,
.swte-flip-leaf._3-digits .swte-leaf-back {
  width: 148px;
}

.swte-flip-top,
.swte-leaf-front {
  border-top-left-radius: 6px;
  border-top-right-radius: 6px;
  margin-bottom: 1px;
  border-bottom-style: solid;
  border-bottom-width: 1px;
}
.swte-flip-top span,
.swte-leaf-front span {
  top: 0;
}

.swte-leaf-front {
  position: absolute;
  z-index: 10;
}

.swte-flip-bottom,
.swte-leaf-back {
  border-bottom-left-radius: 6px;
  border-bottom-right-radius: 6px;
  border-top-style: solid;
  border-top-width: 1px;
}
.swte-flip-bottom span,
.swte-leaf-back span {
  bottom: 0;
}

.swte-leaf-back {
  position: absolute;
  top: 64px;
  z-index: 10;
}

@media screen and (max-width: 600px) {
  .swte-flipclock {
    padding: 6.6666666667vw;
  }

  .swte-leaf {
    margin: 0 0.6666666667vw;
  }

  .swte-leaf::after {
    margin-top: 1.3333333333vw;
    font-size: 2.3333333333vw;
  }

  .swte-flip-top,
  .swte-flip-bottom,
  .swte-leaf-front,
  .swte-leaf-back {
    height: 10.6666666667vw;
    width: 20vw;
  }
  .swte-flip-top span,
  .swte-flip-bottom span,
  .swte-leaf-front span,
  .swte-leaf-back span {
    height: 21.3333333333vw;
    font-size: 13.3333333333vw;
    line-height: 21.3333333333vw;
  }

  .swte-leaf._3-digits .swte-flip-top,
  .swte-leaf._3-digits .swte-flip-bottom,
  .swte-leaf._3-digits .swte-leaf-front,
  .swte-leaf._3-digits .swte-leaf-back {
    width: 24.6666666667vw;
  }

  .swte-flip-top,
  .swte-leaf-front {
    border-top-left-radius: 1vw;
    border-top-right-radius: 1vw;
  }

  .swte-flip-bottom,
  .swte-leaf-back {
    border-bottom-left-radius: 1vw;
    border-bottom-right-radius: 1vw;
  }

  .swte-leaf-back {
    top: 10.6666666667vw;
  }
}
.swte-leaf-front {
  transform-origin: bottom center;
  transform: rotateX(0deg);
  transform-style: preserve-3d;
  transition-delay: 0.3s;
}

.swte-leaf-back {
  transform-origin: top center;
  transform: rotateX(90deg);
  transform-style: preserve-3d;
  transition-delay: 0s;
}

.swte-flip-bottom::before {
  content: "";
  display: block;
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 0%;
  background: black;
  background: linear-gradient(180deg, rgba(0, 0, 0, 0.8) 0%, rgba(0, 0, 0, 0) 100%);
  filter: blur(10px);
}

.swte-flip .swte-leaf-front {
  transform: rotateX(-90deg);
  transition: transform 0.3s ease-in, background-color 0.3s ease-in, color 0.3s ease-in;
  transition-delay: 0s;
  color: gray;
  background-color: #0b0101;
}
.swte-flip .leaf-back {
  transform: rotateX(0deg);
  transition: transform 0.3s ease-in, background-color 0.3s ease-in, color 0.3s ease-in;
  transition-delay: 0.3s;
}
.swte-flip .swte-flip-bottom::before {
  transition: height 0.3s ease-in-out;
  transition-delay: 0.15s;
  height: 100%;
}

</style>
<script>
(function () {
  function FlipClock(el, config) {
    var _this = this;
    var updateTimeout;
    _this.el = el;
    _this.config = Object.assign({
      endDate: new Date((new Date().getFullYear() + 1),0,0),
      labels: {
        days: 'Days',
        hours: 'Hours',
        minutes: 'Minutes',
        seconds: 'Seconds'
      }
    }, config);

    _this.current = {
      d: "00",
      h: "00",
      m: "00",
      s: "00"
    };

    createView();
    updateView();
    addObserver();

    function start() {
      _this.current = getTimeUntil(config.endDate.getTime(), new Date().getTime());
      updateView();
      clearTimeout(updateTimeout);
      updateTimeout = setTimeout(start, 500);
    }

    function stop() {
      clearTimeout(updateTimeout);
    }

    function destroy() {
      stop();
      _this.observer.disconnect();
      _this.el.innerHTML = "";
    }

    function getTimeUntil(dateFuture, dateNow) {
      var delta = Math.abs(dateFuture - dateNow) / 1000;
      var d = Math.floor(delta / 86400);
      delta -= d * 86400;
      var h = Math.floor(delta / 3600) % 24;
      delta -= h * 3600;
      var m = Math.floor(delta / 60) % 60;
      delta -= m * 60;
      var s = Math.floor(delta % 60);

      d = pad2(d);
      h = pad2(h);
      m = pad2(m);
      s = pad2(s);

      return {
        d: d + "",
        h: h + "",
        m: m + "",
        s: s + ""
      };
    }

    // Assumes a non-negative number.
    function pad2(number) {
      if (number < 10) return "0" + number;
      else return "" + number;
    }

    function pad3(number) {
      if (number < 10) return "00" + number;
      else if (number < 100) return "0" + number;
      else return "" + number;
    }

    function createView() {
      _this.daysLeaf = createLeaf(_this.config.labels.days, 3);
      _this.hoursLeaf = createLeaf(_this.config.labels.hours);
      _this.minutesLeaf = createLeaf(_this.config.labels.minutes);
      _this.secondsLeaf = createLeaf(_this.config.labels.seconds);
    }

    function createLeaf(label, digits) {
      var leaf = document.createElement("div");
      leaf.className = "swte-leaf _" + (digits ? digits : "2") + "-digits";
      leaf.setAttribute("data-label", label);
      var top = document.createElement("div");
      var topLabel = document.createElement("span");
      top.className = "swte-flip-top";
      top.appendChild(topLabel);
      var frontLeaf = document.createElement("div");
      var frontLabel = document.createElement("span");
      frontLeaf.className = "swte-leaf-front";
      frontLeaf.appendChild(frontLabel);
      var backLeaf = document.createElement("div");
      var backLabel = document.createElement("span");
      backLeaf.className = "swte-leaf-back";
      backLeaf.appendChild(backLabel);
      var bottom = document.createElement("div");
      var bottomLabel = document.createElement("span");
      bottom.className = "swte-flip-bottom";
      bottom.appendChild(bottomLabel);

      leaf.appendChild(top);
      leaf.appendChild(frontLeaf);
      leaf.appendChild(backLeaf);
      leaf.appendChild(bottom);

      _this.el.appendChild(leaf);

      return {
        el: leaf,
        topLabel: topLabel,
        frontLabel: frontLabel,
        backLabel: backLabel,
        bottomLabel: bottomLabel
      };
    }

    function updateView() {
      updateLeaf(_this.daysLeaf, _this.current.d);
      updateLeaf(_this.hoursLeaf, _this.current.h);
      updateLeaf(_this.minutesLeaf, _this.current.m);
      updateLeaf(_this.secondsLeaf, _this.current.s);
    }

    function updateLeaf(leaf, value) {
      if (leaf.isFlipping) return;

      var currentValue = leaf.topLabel.innerText;
      if (value !== currentValue) {
        leaf.isFlipping = true;
        leaf.topLabel.innerText = value;
        leaf.backLabel.innerText = value;
        leaf.el.classList.add("flip");

        clearTimeout(leaf.timeout);
        leaf.timeout = setTimeout(function () {
          leaf.frontLabel.innerText = value;
          leaf.bottomLabel.innerText = value;
          leaf.el.classList.remove("flip");
        }, 600);

        clearTimeout(leaf.timeout2);
        leaf.timeout2 = setTimeout(function () {
          leaf.isFlipping = false;
        }, 1000);
      }
    }

    function addObserver() {
      _this.observer = new IntersectionObserver(function (entries, observer) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            start();
          } else {
            stop();
          }
        });
      });

      _this.observer.observe(_this.el);
    }

    return {
      start: start,
      stop: stop,
      destroy: destroy,
      getCurrent: function () {
        return _this.current;
      }
    };
  }


  //================================================
  // Initialise flipclock
  var currentYear = new Date().getFullYear();

  new FlipClock(document.getElementById('flipclock-1'), {
    endDate: new Date(2020, 09, 15),
    labels: {
        days: 'Days',
        hours: 'Hours',
        minutes: 'Minutes',
        seconds: 'Seconds'
    }
  });
})();

</script>