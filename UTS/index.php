<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>SkyHigh Luxury - Premium Voyage Experience</title>
    
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
    <script id="tailwind-config">
        tailwind.config = {
          theme: {
            extend: {
              "colors": {
                      "on-surface-variant": "#44474d",
                      "surface-bright": "#f8f9ff",
                      "primary": "#00030a",
                      "secondary-container": "#2dbcfe",
                      "surface": "#f8f9ff",
                      "on-surface": "#0b1c30",
                      "surface-container-low": "#eff4ff",
                      "primary-container": "#0a1d37",
                      "outline-variant": "#c5c6ce",
                      "surface-variant": "#d3e4fe",
                      "surface-container": "#e5eeff",
                      "secondary": "#00658d",
                      "on-secondary": "#ffffff",
                      "background": "#f8f9ff",
                      "surface-container-lowest": "#ffffff",
                      "surface-container-high": "#dce9ff",
              },
              "spacing": { "gutter": "24px", "stack-md": "16px", "stack-lg": "32px", "section-gap": "80px" }
            }
          }
        }
    </script>
    <style>
        body { background-color: #f8f9ff; color: #0b1c30; font-family: 'Inter', sans-serif; overflow-x: hidden; }
        .flight-widget-glass { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(12px); border: 1px solid #e5eeff; }
        .ambient-shadow { box-shadow: 0 4px 20px 0 rgba(0, 3, 10, 0.04); }
        .plane-body { background: rgba(229, 238, 255, 0.3); border-radius: 100px 100px 40px 40px; border: 1px solid #c5c6ce; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        input[type="date"]::-webkit-calendar-picker-indicator { cursor: pointer; opacity: 0.6; }
    </style>
</head>
<body class="antialiased min-h-screen">
    <div id="root"></div>

    <!-- Core Libraries -->
    <script src="https://unpkg.com/react@18/umd/react.production.min.js"></script>
    <script src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js"></script>
    <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/framer-motion/10.16.4/framer-motion.umd.min.js"></script>

    <script type="text/babel">
        const { useState, useEffect } = React;
        const { motion, AnimatePresence } = window.Motion || window.FramerMotion || { 
            motion: { div: 'div', section: 'section', button: 'button', article: 'article' }, 
            AnimatePresence: ({children}) => children 
        };

        // --- SUB-COMPONENTS ---

        const Navbar = ({ onHome }) => (
            <nav className="fixed top-0 w-full z-50 bg-white border-b border-outline-variant h-20 flex items-center px-6 md:px-12 justify-between">
                <div className="text-2xl font-bold text-primary cursor-pointer" onClick={onHome}>SkyHigh Luxury</div>
                <div className="hidden md:flex gap-8">
                    <a href="#" className="text-secondary font-bold border-b-2 border-secondary pb-1 text-sm">Flights</a>
                    <a href="#" className="text-on-surface-variant hover:text-secondary transition-colors text-sm">Hotels</a>
                    <a href="#" className="text-on-surface-variant hover:text-secondary transition-colors text-sm">Experiences</a>
                </div>
                <div className="flex gap-4 text-primary">
                    <button className="p-2 hover:bg-surface-variant rounded-full"><span className="material-symbols-outlined">language</span></button>
                    <button className="p-2 hover:bg-surface-variant rounded-full"><span className="material-symbols-outlined">account_circle</span></button>
                </div>
            </nav>
        );

        const AutocompleteInput = ({ label, icon, placeholder, value, onChange, onSelect }) => {
            const [suggestions, setSuggestions] = useState([]);
            const [show, setShow] = useState(false);

            const handleInput = (e) => {
                const val = e.target.value;
                onChange(val);
                if (val.length >= 2) {
                    fetch(`api.php?action=search_airports&term=${encodeURIComponent(val)}`)
                        .then(res => res.json())
                        .then(data => { setSuggestions(data); setShow(true); });
                } else { setShow(false); }
            };

            return (
                <div className="flex flex-col relative">
                    <label className="text-[10px] font-bold text-on-surface-variant mb-2 ml-1 uppercase">{label}</label>
                    <div className="relative">
                        <span className="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant">{icon}</span>
                        <input className="w-full pl-12 pr-4 py-4 rounded-xl border border-outline-variant bg-white/50 text-sm focus:border-secondary outline-none transition-all"
                            placeholder={placeholder} value={value} onChange={handleInput} onFocus={() => value.length >= 2 && setShow(true)} />
                    </div>
                    {show && suggestions.length > 0 && (
                        <div className="absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-2xl border border-outline-variant z-[100] overflow-hidden">
                            {suggestions.map(s => (
                                <div key={s.code} onClick={() => { onSelect(s.code, `${s.name} (${s.code})`); setShow(false); }} 
                                     className="p-4 hover:bg-surface-variant cursor-pointer border-b border-outline-variant last:border-none flex justify-between">
                                    <span className="font-bold text-secondary">{s.code}</span>
                                    <span className="text-xs text-on-surface-variant">{s.name}</span>
                                </div>
                            ))}
                        </div>
                    )}
                </div>
            );
        };

        const FlightCard = ({ flight, onSelect, index }) => (
            <motion.article initial={{ opacity: 0, x: -20 }} animate={{ opacity: 1, x: 0 }} transition={{ delay: index * 0.1 }}
                className="bg-white p-6 md:p-8 rounded-2xl mb-4 shadow-sm border border-outline-variant flex flex-col md:flex-row justify-between items-center group hover:border-secondary transition-all gap-6">
                <div className="flex items-center gap-6 flex-1 w-full">
                    <div className="w-12 h-12 bg-surface-variant rounded-full flex items-center justify-center shrink-0">
                        <span className="material-symbols-outlined text-primary">flight_class</span>
                    </div>
                    <div className="flex-1">
                        <div className="flex items-center gap-2 mb-2">
                            <h4 className="font-bold text-primary">{flight.airline_full_name}</h4>
                            {index === 0 && <span className="bg-surface-container-low text-secondary px-2 py-0.5 rounded-full text-[10px] font-bold uppercase">Best Value</span>}
                        </div>
                        <div className="flex items-center gap-8">
                            <div className="text-center">
                                <p className="text-xl font-bold text-primary">08:00</p>
                                <p className="text-[10px] text-on-surface-variant font-bold uppercase">{flight.origin}</p>
                            </div>
                            <div className="flex-1 flex flex-col items-center min-w-[100px]">
                                <p className="text-[10px] text-on-surface-variant mb-1">1h 55m</p>
                                <div className="w-full h-[1px] bg-outline-variant relative"><span className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-1.5 h-1.5 bg-secondary rounded-full"></span></div>
                                <p className="text-[10px] text-secondary mt-1 font-bold">Non-stop</p>
                            </div>
                            <div className="text-center">
                                <p className="text-xl font-bold text-primary">10:55</p>
                                <p className="text-[10px] text-on-surface-variant font-bold uppercase">{flight.destination}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="text-right w-full md:w-auto md:pl-8 md:border-l border-outline-variant">
                    <p className="text-[10px] text-on-surface-variant font-bold uppercase mb-1">Economy</p>
                    <p className="text-2xl font-bold text-secondary mb-3">IDR {flight.price.toLocaleString('id-ID')}</p>
                    <button onClick={() => onSelect(flight)} className="w-full md:w-auto bg-secondary text-white px-8 py-2.5 rounded-lg text-xs font-bold hover:bg-primary transition-all">SELECT</button>
                </div>
            </motion.article>
        );

        const ProgressStepper = ({ step }) => (
            <div className="flex items-center justify-between max-w-2xl mx-auto mb-12 px-4">
                {['Penerbangan', 'Penumpang', 'Kursi', 'Pembayaran'].map((label, i) => {
                    const n = i + 1;
                    return (
                        <React.Fragment key={i}>
                            <div className="flex flex-col items-center gap-2">
                                <div className={`w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all ${step >= n ? 'bg-secondary text-white' : 'border-2 border-outline-variant text-outline-variant'}`}>
                                    {step > n ? <span className="material-symbols-outlined text-sm">check</span> : n}
                                </div>
                                <span className={`text-[9px] uppercase font-bold tracking-widest ${step === n ? 'text-secondary' : 'text-outline-variant'}`}>{label}</span>
                            </div>
                            {i < 3 && <div className={`flex-grow h-px mx-2 md:mx-4 ${step > n ? 'bg-secondary' : 'bg-outline-variant'}`}></div>}
                        </React.Fragment>
                    );
                })}
            </div>
        );

        const SeatMap = ({ onSelect, selectedSeat }) => {
            const occupied = ['1C', '2H', '12D'];
            return (
                <div className="plane-body max-w-md mx-auto p-8 md:p-12 relative">
                    <div className="space-y-12">
                        <div className="text-center">
                            <div className="text-[10px] font-black text-secondary uppercase tracking-[0.3em] mb-8">Business Class</div>
                            {[1, 2].map(r => (
                                <div key={r} className="flex justify-center gap-6 md:gap-10 mb-6">
                                    <div className="flex gap-2">
                                        {['A', 'C'].map(c => {
                                            const id = `${r}${c}`;
                                            const isOccupied = occupied.includes(id);
                                            return (
                                                <button key={id} onClick={() => !isOccupied && onSelect(id)} className={`w-10 h-12 md:w-12 md:h-14 rounded-lg flex items-center justify-center transition-all border ${selectedSeat === id ? 'bg-secondary text-white border-secondary' : isOccupied ? 'bg-surface-variant opacity-50 cursor-not-allowed border-outline-variant' : 'bg-white border-outline-variant hover:border-secondary'}`}>
                                                    <span className="text-[9px] font-bold">{isOccupied ? '×' : id}</span>
                                                </button>
                                            );
                                        })}
                                    </div>
                                    <div className="w-4 flex items-center justify-center text-[9px] font-bold text-outline-variant">{r}</div>
                                    <div className="flex gap-2">
                                        {['H', 'K'].map(c => {
                                            const id = `${r}${c}`;
                                            const isOccupied = occupied.includes(id);
                                            return (
                                                <button key={id} onClick={() => !isOccupied && onSelect(id)} className={`w-10 h-12 md:w-12 md:h-14 rounded-lg flex items-center justify-center transition-all border ${selectedSeat === id ? 'bg-secondary text-white border-secondary' : isOccupied ? 'bg-surface-variant opacity-50 cursor-not-allowed border-outline-variant' : 'bg-white border-outline-variant hover:border-secondary'}`}>
                                                    <span className="text-[9px] font-bold">{isOccupied ? '×' : id}</span>
                                                </button>
                                            );
                                        })}
                                    </div>
                                </div>
                            ))}
                        </div>
                        <div className="text-center pt-8 border-t border-outline-variant/30">
                            <div className="text-[10px] font-black text-on-surface-variant uppercase tracking-[0.3em] mb-8">Economy Class</div>
                            {[12, 13, 14].map(r => (
                                <div key={r} className="flex justify-center gap-6 md:gap-10 mb-4">
                                    <div className="flex gap-1.5">
                                        {['A', 'B', 'C'].map(c => {
                                            const id = `${r}${c}`;
                                            const isOccupied = occupied.includes(id);
                                            return (
                                                <button key={id} onClick={() => !isOccupied && onSelect(id)} className={`w-8 h-8 md:w-9 md:h-9 rounded flex items-center justify-center transition-all border ${selectedSeat === id ? 'bg-secondary text-white border-secondary' : isOccupied ? 'bg-surface-variant opacity-50 cursor-not-allowed border-outline-variant' : 'bg-white border-outline-variant hover:border-secondary'}`}>
                                                    <span className="text-[8px] font-bold">{isOccupied ? '×' : id}</span>
                                                </button>
                                            );
                                        })}
                                    </div>
                                    <div className="w-4 flex items-center justify-center text-[9px] font-bold text-outline-variant">{r}</div>
                                    <div className="flex gap-1.5">
                                        {['H', 'J', 'K'].map(c => {
                                            const id = `${r}${c}`;
                                            const isOccupied = occupied.includes(id);
                                            return (
                                                <button key={id} onClick={() => !isOccupied && onSelect(id)} className={`w-8 h-8 md:w-9 md:h-9 rounded flex items-center justify-center transition-all border ${selectedSeat === id ? 'bg-secondary text-white border-secondary' : isOccupied ? 'bg-surface-variant opacity-50 cursor-not-allowed border-outline-variant' : 'bg-white border-outline-variant hover:border-secondary'}`}>
                                                    <span className="text-[8px] font-bold">{isOccupied ? '×' : id}</span>
                                                </button>
                                            );
                                        })}
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            );
        };

        // --- MAIN APP ---

        const App = () => {
            const [view, setView] = useState('home'); 
            const [loading, setLoading] = useState(false);
            const [results, setResults] = useState([]);
            const [params, setParams] = useState({ origin: '', originText: '', destination: '', destinationText: '', date: '', currency: 'IDR' });
            const [selectedFlight, setSelectedFlight] = useState(null);
            const [selectedSeat, setSelectedSeat] = useState(null);

            const handleSearch = (e) => {
                e.preventDefault();
                setLoading(true); setView('results');
                fetch(`api.php?action=search_flights&origin=${params.origin}&destination=${params.destination}&departure_at=${params.date}&currency=${params.currency}`)
                    .then(res => res.json()).then(data => { setResults(data.data || []); setLoading(false); })
                    .catch(() => setLoading(false));
            };

            const handleSelectFlight = (flight) => {
                setSelectedFlight(flight);
                setSelectedSeat(null);
                setView('seat');
                window.scrollTo(0, 0);
            };

            const handleConfirmSeat = () => {
                if (!selectedSeat || !selectedFlight) return;
                setView('payment');
                window.scrollTo(0, 0);
            };

            const handleStartOver = () => {
                setSelectedFlight(null);
                setSelectedSeat(null);
                setView('home');
                window.scrollTo(0, 0);
            };

            const handleProceedToPayment = () => {
                if (!selectedFlight || !selectedSeat) return;
                const query = new URLSearchParams({
                    airline: selectedFlight.airline_full_name || selectedFlight.airline || 'Unknown',
                    flight: selectedFlight.flight_number || '-',
                    price: String(selectedFlight.price || 0),
                    currency: params.currency,
                    origin: selectedFlight.origin || params.origin,
                    destination: selectedFlight.destination || params.destination,
                    seat: selectedSeat,
                });
                window.location.href = `booking.php?${query.toString()}`;
            };

            return (
                <div className="flex flex-col min-h-screen">
                    <Navbar onHome={handleStartOver} />
                    
                    <main className="flex-grow pt-20">
                        <AnimatePresence mode="wait">
                            {view === 'home' && (
                                <motion.div key="home" initial={{ opacity: 0 }} animate={{ opacity: 1 }} exit={{ opacity: 0 }}>
                                    <section className="relative min-h-[700px] flex items-center justify-center px-6 bg-cover bg-center" style={{ backgroundImage: "url('https://lh3.googleusercontent.com/aida-public/AB6AXuCznBMae4VAbXGN2WW0Msaw2ZKy8ttkXhqfeKHXt1m62kvZH8KNIaoo1Z59K20P2DuScCHL4GKWkPkTD7yQku6Zun__7FDbJDWF8XX27Hy1eaqABMd0sQv6IEwH9mio-78Yh9hCTFVFQlVXCaKg6WLukonmHLHWzXJfcqO2OaNkTOIqwPuVjnivO88og5uxeDFJ9wo9e0Kq850z2e8z_qZh8xsQojtFQ31fbARyVP3Ry6t_Qg_5cieUNGMQAremgJ8CWeOCMQhhqx0')" }}>
                                        <div className="absolute inset-0 bg-primary/20 backdrop-blur-[2px]"></div>
                                        <div className="relative z-10 w-full max-w-5xl mx-auto">
                                            <h1 className="text-4xl md:text-7xl font-bold text-white mb-12 text-center drop-shadow-xl">Voyage in Elegance</h1>
                                            <div className="flight-widget-glass rounded-3xl p-8 md:p-12 shadow-2xl">
                                                <form onSubmit={handleSearch} className="grid grid-cols-1 md:grid-cols-12 gap-6">
                                                    <div className="md:col-span-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                        <AutocompleteInput label="Origin" icon="flight_takeoff" placeholder="Where from?" value={params.originText} onChange={(v)=>setParams({...params, originText:v})} onSelect={(c,t)=>setParams({...params, origin:c, originText:t})} />
                                                        <AutocompleteInput label="Destination" icon="flight_land" placeholder="Where to?" value={params.destinationText} onChange={(v)=>setParams({...params, destinationText:v})} onSelect={(c,t)=>setParams({...params, destination:c, destinationText:t})} />
                                                    </div>
                                                    <div className="md:col-span-3">
                                                        <label className="text-[10px] font-bold text-on-surface-variant mb-2 ml-1 uppercase block">Departure</label>
                                                        <div className="relative">
                                                            <span className="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant">calendar_today</span>
                                                            <input type="date" className="w-full pl-12 pr-4 py-4 rounded-xl border border-outline-variant bg-white/50 text-sm focus:border-secondary outline-none transition-all" value={params.date} onChange={(e)=>setParams({...params, date:e.target.value})} required />
                                                        </div>
                                                    </div>
                                                    <div className="md:col-span-3 flex flex-col justify-end">
                                                        <button type="submit" className="bg-secondary text-white font-bold py-4 rounded-xl hover:bg-primary transition-all shadow-lg flex items-center justify-center gap-2">Search <span className="material-symbols-outlined">arrow_forward</span></button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </section>
                                </motion.div>
                            )}

                            {view === 'results' && (
                                <motion.div key="results" initial={{ opacity: 0 }} animate={{ opacity: 1 }} exit={{ opacity: 0 }}>
                                    <header className="bg-primary-container text-white py-12 px-6">
                                        <div className="max-w-5xl mx-auto flex flex-col md:flex-row justify-between items-center gap-6">
                                            <div>
                                                <h1 className="text-3xl font-bold flex items-center gap-3">{params.originText.split('(')[0]} <span className="material-symbols-outlined text-secondary-container">flight_takeoff</span> {params.destinationText.split('(')[0]}</h1>
                                                <p className="text-sm opacity-70 mt-1">{params.date} • 1 Passenger • Economy</p>
                                            </div>
                                            <button onClick={() => setView('home')} className="px-6 py-2.5 bg-secondary text-white rounded-lg text-xs font-bold hover:bg-secondary-container transition-colors">Modify Search</button>
                                        </div>
                                    </header>
                                    <div className="max-w-5xl mx-auto py-12 px-6">
                                        <ProgressStepper step={1} />
                                        {loading ? <div className="text-center py-20 animate-pulse text-secondary font-bold tracking-widest text-xs">SCANNING SKIES...</div> : 
                                        results.length > 0 ? results.map((f, i) => <FlightCard key={i} flight={f} index={i} onSelect={handleSelectFlight} />) : 
                                        <div className="text-center py-20 bg-white rounded-2xl border border-outline-variant"><p className="text-on-surface-variant text-sm">No voyages found for this date.</p></div>}
                                    </div>
                                </motion.div>
                            )}

                            {view === 'seat' && (
                                <motion.div key="seat" initial={{ opacity: 0, y: 20 }} animate={{ opacity: 1, y: 0 }} className="max-w-6xl mx-auto py-12 px-6 grid grid-cols-1 lg:grid-cols-12 gap-12">
                                    <div className="lg:col-span-12"><ProgressStepper step={3} /></div>
                                    <div className="lg:col-span-8">
                                        <div className="bg-white p-8 md:p-12 rounded-3xl border border-outline-variant shadow-sm">
                                            <div className="flex justify-center gap-8 mb-12 flex-wrap">
                                                <div className="flex items-center gap-2"><div className="w-4 h-4 bg-white border border-outline-variant rounded"></div><span className="text-[10px] font-bold text-on-surface-variant uppercase">Available</span></div>
                                                <div className="flex items-center gap-2"><div className="w-4 h-4 bg-secondary rounded"></div><span className="text-[10px] font-bold text-on-surface-variant uppercase">Selected</span></div>
                                                <div className="flex items-center gap-2"><div className="w-4 h-4 bg-surface-variant opacity-50 rounded"></div><span className="text-[10px] font-bold text-on-surface-variant uppercase">Occupied</span></div>
                                            </div>
                                            <SeatMap onSelect={setSelectedSeat} selectedSeat={selectedSeat} />
                                        </div>
                                    </div>
                                    <div className="lg:col-span-4 flex flex-col gap-6">
                                        <div className="bg-white p-8 rounded-3xl border border-outline-variant shadow-sm">
                                            <h3 className="text-xl font-bold mb-6 flex justify-between items-center">Seat Details {selectedSeat && <span className="bg-secondary text-white px-3 py-1 rounded-full text-xs">{selectedSeat}</span>}</h3>
                                            {selectedSeat ? (
                                                <div className="space-y-6">
                                                    <div className="flex gap-4 items-center"><span className="material-symbols-outlined text-secondary">star</span><div><p className="text-xs font-bold">{parseInt(selectedSeat) < 10 ? 'Business Class' : 'Economy Class'}</p><p className="text-[10px] text-on-surface-variant">Premium Experience</p></div></div>
                                                    <div className="flex gap-4 items-center"><span className="material-symbols-outlined text-secondary">airline_seat_legroom_extra</span><div><p className="text-xs font-bold">Extra Legroom</p><p className="text-[10px] text-on-surface-variant">Pitch 78 inches</p></div></div>
                                                    <div className="pt-6 border-t border-outline-variant">
                                                        <p className="text-[10px] font-bold text-on-surface-variant uppercase mb-4">Amenities</p>
                                                        <div className="flex flex-wrap gap-2">
                                                            {['Power', 'USB', 'WiFi', '18" Screen'].map(a => <span key={a} className="bg-surface-container px-3 py-1 rounded text-[10px] font-medium">{a}</span>)}
                                                        </div>
                                                    </div>
                                                </div>
                                            ) : (
                                                <p className="text-xs text-on-surface-variant text-center py-12">Please select a seat from the map.</p>
                                            )}
                                        </div>
                                        <div className="bg-surface-container-low p-8 rounded-3xl border border-outline-variant mt-auto">
                                            <div className="flex justify-between items-end mb-6">
                                                <div><p className="text-[10px] font-bold text-on-surface-variant uppercase">Total Price</p><p className="text-2xl font-bold text-primary">IDR {selectedFlight?.price.toLocaleString('id-ID')}</p></div>
                                                <div className="text-right"><p className="text-[10px] font-bold text-on-surface-variant uppercase">Passenger 1</p><p className="text-sm font-bold text-secondary">{selectedSeat || 'No Seat'}</p></div>
                                            </div>
                                            <button onClick={handleConfirmSeat} className="w-full bg-secondary text-white py-4 rounded-xl font-bold hover:bg-primary transition-all disabled:opacity-50" disabled={!selectedSeat}>Confirm Selection</button>
                                        </div>
                                    </div>
                                </motion.div>
                            )}

                            {view === 'payment' && (
                                <motion.div key="payment" initial={{ opacity: 0, y: 20 }} animate={{ opacity: 1, y: 0 }} className="max-w-5xl mx-auto py-12 px-6">
                                    <ProgressStepper step={4} />
                                    <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
                                        <div className="lg:col-span-8 bg-white p-8 md:p-10 rounded-3xl border border-outline-variant shadow-sm">
                                            <div className="w-16 h-16 rounded-full bg-surface-container flex items-center justify-center mb-6">
                                                <span className="material-symbols-outlined text-secondary text-3xl">check_circle</span>
                                            </div>
                                            <h2 className="text-3xl font-bold text-primary mb-3">Seat selection confirmed</h2>
                                            <p className="text-sm text-on-surface-variant mb-8">Your seat has been reserved for this itinerary. Continue with payment or update the seat if needed.</p>
                                            <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                                <div className="bg-surface-container-low p-5 rounded-xl border border-outline-variant">
                                                    <p className="text-[10px] font-bold uppercase text-on-surface-variant mb-2">Route</p>
                                                    <p className="font-bold text-primary">{selectedFlight?.origin} to {selectedFlight?.destination}</p>
                                                </div>
                                                <div className="bg-surface-container-low p-5 rounded-xl border border-outline-variant">
                                                    <p className="text-[10px] font-bold uppercase text-on-surface-variant mb-2">Seat</p>
                                                    <p className="font-bold text-secondary">{selectedSeat}</p>
                                                </div>
                                                <div className="bg-surface-container-low p-5 rounded-xl border border-outline-variant">
                                                    <p className="text-[10px] font-bold uppercase text-on-surface-variant mb-2">Class</p>
                                                    <p className="font-bold text-primary">{parseInt(selectedSeat) < 10 ? 'Business' : 'Economy'}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div className="lg:col-span-4 bg-surface-container-low p-8 rounded-3xl border border-outline-variant h-fit">
                                            <p className="text-[10px] font-bold uppercase text-on-surface-variant mb-2">Total Price</p>
                                            <p className="text-3xl font-bold text-primary mb-8">IDR {selectedFlight?.price.toLocaleString('id-ID')}</p>
                                            <button onClick={handleProceedToPayment} className="w-full bg-secondary text-white py-4 rounded-xl font-bold hover:bg-primary transition-all mb-3">Proceed to Payment</button>
                                            <button onClick={() => setView('seat')} className="w-full bg-white text-primary py-4 rounded-xl font-bold border border-outline-variant hover:border-secondary transition-all">Change Seat</button>
                                        </div>
                                    </div>
                                </motion.div>
                            )}
                        </AnimatePresence>
                    </main>

                    <footer className="bg-primary-container p-12 text-on-primary-container mt-auto">
                        <div className="max-w-5xl mx-auto flex flex-col md:flex-row justify-between items-center gap-6 text-[10px] opacity-60">
                            <div>© 2024 SkyHigh Luxury. All rights reserved.</div>
                            <div className="flex gap-8"><span>Privacy Policy</span><span>Terms of Service</span><span>Customer Support</span></div>
                        </div>
                    </footer>
                </div>
            );
        };

        const root = ReactDOM.createRoot(document.getElementById('root'));
        root.render(<App />);
    </script>
</body>
</html>
