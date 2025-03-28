export default {
    async fetch(request, env, ctx) {
      // Handle CORS preflight
      if (request.method === "OPTIONS") {
        return new Response(null, {
          headers: {
            "Access-Control-Allow-Origin": "*",
            "Access-Control-Allow-Methods": "GET, OPTIONS",
            "Access-Control-Allow-Headers": "Content-Type",
          },
        });
      }
  
      const today = new Date().toISOString().split('T')[0];
      const week = getWeekNumber(new Date());
      const month = new Date().toISOString().slice(0, 7);
  
      // Increment counters
      await Promise.all([
        incrementCounter(env.VISITOR_COUNTER, 'today_' + today),
        incrementCounter(env.VISITOR_COUNTER, 'week_' + week),
        incrementCounter(env.VISITOR_COUNTER, 'month_' + month),
        incrementCounter(env.VISITOR_COUNTER, 'total')
      ]);
  
      // Get counter values
      const [todayCount, weekCount, monthCount, totalCount] = await Promise.all([
        env.VISITOR_COUNTER.get('today_' + today) || '0',
        env.VISITOR_COUNTER.get('week_' + week) || '0',
        env.VISITOR_COUNTER.get('month_' + month) || '0',
        env.VISITOR_COUNTER.get('total') || '0'
      ]);
  
      return new Response(JSON.stringify([
        parseInt(todayCount),
        parseInt(weekCount),
        parseInt(monthCount),
        parseInt(totalCount)
      ]), {
        headers: {
          'Content-Type': 'application/json',
          'Access-Control-Allow-Origin': '*'
        }
      });
    }
  };
  
  async function incrementCounter(KV, key) {
    const value = await KV.get(key) || '0';
    await KV.put(key, (parseInt(value) + 1).toString());
  }
  
  function getWeekNumber(date) {
    const d = new Date(date);
    d.setHours(0, 0, 0, 0);
    d.setDate(d.getDate() + 4 - (d.getDay() || 7));
    const yearStart = new Date(d.getFullYear(), 0, 1);
    const weekNo = Math.ceil((((d - yearStart) / 86400000) + 1) / 7);
    return d.getFullYear() + '-W' + weekNo;
  }