/* Admin Reports - D3 helpers
   Exports: renderBarChart(containerSelector, data, opts), renderLineChart(containerSelector, data, opts)
   Expects data for bar chart: [{ label: 'Category A', value: 10 }, ...]
   Expects data for line chart: [{ date: '2024-01-01', value: 12 }, ...]
*/
import * as d3 from "https://cdn.jsdelivr.net/npm/d3@7/+esm";

function clearContainer(sel) {
  const el = document.querySelector(sel);
  if (!el) return null;
  el.innerHTML = "";
  return el;
}

export function renderBarChart(containerSelector, data, opts = {}) {
  const container = clearContainer(containerSelector);
  if (!container) return;

  const margin = { top: 12, right: 12, bottom: 40, left: 60 };
  const width = opts.width || Math.max(480, container.clientWidth || 640);
  const height = opts.height || 320;

  const svg = d3.create("svg").attr("width", width).attr("height", height);

  const x = d3
    .scaleBand()
    .domain(data.map((d) => d.label))
    .range([margin.left, width - margin.right])
    .padding(0.2);

  const y = d3
    .scaleLinear()
    .domain([0, d3.max(data, (d) => d.value) || 1])
    .nice()
    .range([height - margin.bottom, margin.top]);

  const xAxis = (g) =>
    g
      .attr("transform", `translate(0,${height - margin.bottom})`)
      .call(d3.axisBottom(x))
      .selectAll("text")
      .attr("transform", "rotate(-30)")
      .style("text-anchor", "end");

  const yAxis = (g) =>
    g
      .attr("transform", `translate(${margin.left},0)`)
      .call(d3.axisLeft(y).ticks(5));

  svg.append("g").call(xAxis);
  svg.append("g").call(yAxis);

  svg
    .append("g")
    .selectAll("rect")
    .data(data)
    .join("rect")
    .attr("x", (d) => x(d.label))
    .attr("y", (d) => y(d.value))
    .attr("height", (d) => y(0) - y(d.value))
    .attr("width", x.bandwidth())
    .attr("fill", opts.color || "#0d6efd");

  container.appendChild(svg.node());
}

export function renderLineChart(containerSelector, data, opts = {}) {
  const container = clearContainer(containerSelector);
  if (!container) return;

  const margin = { top: 12, right: 20, bottom: 30, left: 50 };
  const width = opts.width || Math.max(640, container.clientWidth || 800);
  const height = opts.height || 320;

  // parse dates (accept plain YYYY-MM-DD or ISO datetimes)
  const parse = (str) => {
    if (!str) return null;
    // Try YYYY-MM-DD first
    let p = d3.utcParse("%Y-%m-%d")(String(str).slice(0, 10));
    if (p) return p;
    // Fallback to ISO parse which handles datetimes
    try {
      return d3.isoParse(str);
    } catch (e) {
      return null;
    }
  };
  const series = data.map((d) => ({ date: parse(d.date), value: +d.value }));

  const validSeries = series.filter((d) => d.date && !isNaN(d.value));
  if (!validSeries.length) {
    const msg = document.createElement("div");
    msg.className = "text-muted small p-3";
    msg.textContent = "No data available";
    container.appendChild(msg);
    return;
  }
  const x = d3
    .scaleUtc()
    .domain(d3.extent(series, (d) => d.date))
    .domain(d3.extent(validSeries, (d) => d.date));

  const y = d3
    .scaleLinear()
    .domain([0, d3.max(series, (d) => d.value) || 1])
    .nice()
    .range([height - margin.bottom, margin.top]);

  const svg = d3.create("svg").attr("width", width).attr("height", height);

  svg
    .append("g")
    .attr("transform", `translate(0,${height - margin.bottom})`)
    .call(d3.axisBottom(x).ticks(Math.min(8, data.length)));

  svg
    .append("g")
    .attr("transform", `translate(${margin.left},0)`)
    .call(d3.axisLeft(y).ticks(5));

  const line = d3
    .line()
    .defined((d) => d.date && !isNaN(d.value))
    .x((d) => x(d.date))
    .y((d) => y(d.value));

  svg
    .append("path")
    .datum(series)
    .attr("fill", "none")
    .attr("stroke", opts.color || "#198754")
    .attr("stroke-width", 2)
    .attr("d", line);

  svg
    .append("g")
    .selectAll("circle")
    .data(series)
    .data(validSeries)
    .attr("cx", (d) => x(d.date))
    .attr("cy", (d) => y(d.value))
    .attr("r", 3)
    .attr("fill", opts.color || "#198754");

  container.appendChild(svg.node());
}

// Multi-line chart: expects series = [{ name: 'Lost', color:'#dc3545', data: [{date:'2024-01-01', value: 3}, ...] }, ...]
export function renderMultiLineChart(
  containerSelector,
  seriesArray,
  opts = {}
) {
  const container = clearContainer(containerSelector);
  if (!container) return;

  const margin = { top: 24, right: 20, bottom: 40, left: 50 };
  const width = opts.width || Math.max(640, container.clientWidth || 800);
  const height = opts.height || 360;

  // parse dates helper
  const parseDate = (str) => {
    if (!str) return null;
    let p = d3.utcParse("%Y-%m-%d")(String(str).slice(0, 10));
    if (p) return p;
    try {
      return d3.isoParse(str);
    } catch (e) {
      return null;
    }
  };

  // flatten all points
  const allPoints = [];
  (seriesArray || []).forEach((s) => {
    (s.data || []).forEach((d) => {
      const date = parseDate(d.date);
      const value = +d.value;
      if (date && !isNaN(value)) allPoints.push({ date, value });
    });
  });

  if (!allPoints.length) {
    const msg = document.createElement("div");
    msg.className = "text-muted small p-3";
    msg.textContent = "No data available";
    container.appendChild(msg);
    return;
  }

  const x = d3
    .scaleUtc()
    .domain(d3.extent(allPoints, (d) => d.date))
    .range([margin.left, width - margin.right]);

  const y = d3
    .scaleLinear()
    .domain([0, d3.max(allPoints, (d) => d.value) || 1])
    .nice()
    .range([height - margin.bottom, margin.top]);

  const svg = d3.create("svg").attr("width", width).attr("height", height);

  svg
    .append("g")
    .attr("transform", `translate(0,${height - margin.bottom})`)
    .call(d3.axisBottom(x).ticks(Math.min(10, allPoints.length)));

  svg
    .append("g")
    .attr("transform", `translate(${margin.left},0)`)
    .call(d3.axisLeft(y).ticks(6));

  const lineGen = d3
    .line()
    .defined((d) => d.date && !isNaN(d.value))
    .x((d) => x(d.date))
    .y((d) => y(d.value));

  // draw each series
  seriesArray.forEach((s) => {
    const pts = (s.data || [])
      .map((d) => ({ date: parseDate(d.date), value: +d.value }))
      .filter((d) => d.date && !isNaN(d.value));
    svg
      .append("path")
      .datum(pts)
      .attr("fill", "none")
      .attr("stroke", s.color || opts.color || "#198754")
      .attr("stroke-width", 2)
      .attr("d", lineGen);

    svg
      .append("g")
      .selectAll("circle")
      .data(pts)
      .join("circle")
      .attr("cx", (d) => x(d.date))
      .attr("cy", (d) => y(d.value))
      .attr("r", 3)
      .attr("fill", s.color || opts.color || "#198754");
  });

  // Tooltip
  const tooltip = document.createElement("div");
  tooltip.className =
    "d-none position-absolute small bg-dark text-white rounded px-2 py-1";
  tooltip.style.pointerEvents = "none";
  container.style.position = container.style.position || "relative";
  container.appendChild(tooltip);

  // Overlay for mouse events
  const overlay = svg
    .append("rect")
    .attr("x", margin.left)
    .attr("y", margin.top)
    .attr("width", width - margin.left - margin.right)
    .attr("height", height - margin.top - margin.bottom)
    .attr("fill", "transparent");

  overlay.on("mousemove", (event) => {
    const [mx] = d3.pointer(event);
    const xDate = x.invert(mx);
    // find nearest points per series
    const items = seriesArray
      .map((s) => {
        const pts = (s.data || [])
          .map((d) => ({ date: parseDate(d.date), value: +d.value }))
          .filter((d) => d.date && !isNaN(d.value));
        let nearest = null;
        let minDiff = Infinity;
        pts.forEach((p) => {
          const diff = Math.abs(p.date - xDate);
          if (diff < minDiff) {
            minDiff = diff;
            nearest = p;
          }
        });
        return { name: s.name, color: s.color, point: nearest };
      })
      .filter((x) => x.point);

    if (!items.length) {
      tooltip.classList.add("d-none");
      return;
    }

    // position tooltip near mouse
    tooltip.innerHTML =
      `<div class="fw-semibold">${d3.utcFormat("%b %d, %Y")(xDate)}</div>` +
      items
        .map(
          (it) =>
            `<div><span style="display:inline-block;width:10px;height:10px;background:${it.color};margin-right:6px;border-radius:2px;"></span>${it.name}: <strong>${it.point.value}</strong></div>`
        )
        .join("");
    tooltip.style.left =
      Math.min(width - 120, d3.pointer(event, container)[0] + 12) + "px";
    tooltip.style.top =
      Math.max(6, d3.pointer(event, container)[1] - 28) + "px";
    tooltip.classList.remove("d-none");
  });

  overlay.on("mouseleave", () => tooltip.classList.add("d-none"));

  // Legend
  const legend = svg
    .append("g")
    .attr(
      "transform",
      `translate(${width - margin.right - 120},${margin.top - 6})`
    );
  seriesArray.forEach((s, i) => {
    const g = legend.append("g").attr("transform", `translate(0,${i * 18})`);
    g.append("rect")
      .attr("width", 10)
      .attr("height", 10)
      .attr("fill", s.color || opts.color || "#198754");
    g.append("text")
      .attr("x", 14)
      .attr("y", 9)
      .attr("fill", "#fff")
      .attr("font-size", 12)
      .text(s.name || `Series ${i + 1}`);
  });

  container.appendChild(svg.node());

  return { svg, tooltip };
}

// -- simple registry for responsive redraws --
const _registry = [];
export function registerChartRerender(containerSelector, renderFn, args) {
  _registry.push({ containerSelector, renderFn, args });
}

let _resizeTimer = null;
window.addEventListener("resize", () => {
  if (_resizeTimer) clearTimeout(_resizeTimer);
  _resizeTimer = setTimeout(() => {
    _registry.forEach((item) => {
      try {
        item.renderFn.apply(null, item.args);
      } catch (e) {
        console.warn("Chart rerender failed", e);
      }
    });
  }, 200);
});

export default { renderBarChart, renderLineChart };
