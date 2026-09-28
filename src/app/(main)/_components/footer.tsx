
export default function Footer() {
  return (
    <footer className="w-full bg-black text-white">
      <div className="max-w-6xl mx-auto px-4 py-12">
        <div className="grid grid-cols-1 gap-8 md:grid-cols-3 border-b pb-16 border-white">
          <div>
            <h3 className="text-2xl font-bold text-primary">PanDev</h3>
            <p className="mt-2 ">
              Digital solutions, where ideas becomes reality
            </p>
          </div>
          <div>
            <h4 className="font-semibold">Layanan</h4>
            <ul className="mt-2 space-y-1 text-muted/70">
              <li>Web Application</li>
              <li>Mobile Application</li>
              <li>Desktop Application</li>
              <li>Cyber Security Tools</li>
              <li>Design & Multimedia</li>
            </ul>
          </div>
          <div>
            <h4 className="font-semibold">Kontak</h4>
            <ul className="mt-2 space-y-1 text-muted/70"> 
              <li>Email: info@pandev.dev</li>
              <li>Instagram: @pandev.dev</li>
              <li>GitHub: pandev-dev</li>
            </ul>
          </div>
        </div>
        
        <div className="text-center text-sm text-muted pt-8">
          &copy; {new Date().getFullYear()} PanDev. All rights reserved.
        </div>
      </div>
    </footer>
  );
}
