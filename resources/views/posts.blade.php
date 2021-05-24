<html>
  <head>
    <script
      src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.1/dist/alpine.min.js"
      defer
    ></script>
    <link rel="stylesheet" href="{{ asset('styles.css') }}" />
  </head>

  <body>
    <div class="main" x-data="init()">
      <h4 class="font-xxlarge">用 Alpine.js 來尋找文章</h4>
      <div class="searchArea">
        <input
          class="inputText"
          type="text"
          placeholder="請輸入關鍵字"
          x-model="q"
        />
        <button class="bg-default" @click="search()">尋找</button>
      </div>
      <div>
        <template x-for="result in results">
          <div class="postCard">
            <div>
              <img x-bind:src="result.pic" />
            </div>
            <div>
              <div class="postDetailItem">
                <span style="padding-right: 5px">標題:</span
                ><span><b x-text="result.title">三國志</b></span>
              </div>
              <div class="postDetailItem">
                <span style="padding-right: 5px">內容:</span
                ><p x-html="result.content">Hello,World</p>
              </div>
            </div>
          </div>
        </template>
      </div>
    </div>
    <script>
      function init() {
        return {
          results: [],
          q: "",
          search: function () {
            fetch(
              "http://demo_api.test/api/posts/query?s=" +
                this.q 
            )
              .then((response) => response.json())
              .then((response) => (this.results = response.data))
              .catch((err) => console.log(err));
          },
        };
      }
    </script>
  </body>
</html>