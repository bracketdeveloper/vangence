<div id="labelArea">YOUR </div>
<button onclick="printLabel()">print</button>

<script>
    function fitText(el) {
        let size = 100;
        el.style.fontSize = size + "px";

        while (el.scrollWidth > el.clientWidth || el.scrollHeight > el.clientHeight) {
            size--;
            el.style.fontSize = size + "px";
            if (size <= 5) break;
        }
    }

    function printLabel() {
        const text = document.getElementById("labelArea").innerHTML;

        const style = `
    <style>
      @page { size: 2in 1in; margin: 0; }
      body { margin: 0; }
      .label {
        width: 2in;
        height: 1in;
        display: flex;
        justify-content: center;
        align-items: center;
        text-align: center;
        overflow: hidden;
        line-height: 1;
      }
    </style>
  `;

        const w = window.open("", "", "width=400,height=250");
        w.document.write(style + `<div class="label" id="auto">${text}</div>`);
        w.document.close();

        const el = w.document.getElementById("auto");
        fitText(el);

        w.print();
        w.close();
    }
</script>