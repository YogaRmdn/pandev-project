import Image from "next/image";

const TECH_STACKS = [
  { name: "Next.js", icon: "/assets/tech-stacks/nextjs.svg" },
  { name: "Flutter", icon: "/assets/tech-stacks/flutter.svg" },
  { name: "MySQL", icon: "/assets/tech-stacks/mysql.svg" },
  { name: "PostgreSQL", icon: "/assets/tech-stacks/postgresql.svg" },
  { name: "Arduino", icon: "/assets/tech-stacks/arduino.svg" },
  { name: "Burp Suite", icon: "/assets/tech-stacks/burpsuite.svg" },
  { name: "Kali Linux", icon: "/assets/tech-stacks/kali-linux.svg" },
  { name: "Photoshop", icon: "/assets/tech-stacks/photoshop.svg" },
  { name: "Adobe Premiere", icon: "/assets/tech-stacks/adobe-premiere.svg" },
  { name: "VS Code", icon: "/assets/tech-stacks/visual-studio-cde.svg" },
  { name: "ChatGPT", icon: "/assets/tech-stacks/chatgpt.svg" },
  { name: "Gemini", icon: "/assets/tech-stacks/gemini.svg" },
];

const RADIUS_DESKTOP = 280;
const ICON_SIZE_DESKTOP = 72;

export default function TechStacksSection() {
  const total = TECH_STACKS.length;
  const angleStep = (2 * Math.PI) / total;

  const getPositions = (radius: number) =>
    TECH_STACKS.map((_, i) => {
      const angle = angleStep * i - Math.PI / 2;
      return {
        x: Math.cos(angle) * radius,
        y: Math.sin(angle) * radius,
      };
    });

  const desktopPositions = getPositions(RADIUS_DESKTOP);

  return (
    <section
      id="tech-stacks-section"
      className="relative flex items-center justify-center w-full min-h-[600px] py-24 bg-center bg-no-repeat bg-cover"
      style={{ backgroundImage: "url('/assets/common/tech-stacks-background.jpg')" }}
    >
      <div className="relative flex items-center justify-center">
        {/* SVG Lines - Desktop */}
        <svg
          className="absolute hidden md:block"
          width={RADIUS_DESKTOP * 2 + ICON_SIZE_DESKTOP}
          height={RADIUS_DESKTOP * 2 + ICON_SIZE_DESKTOP}
          style={{
            left: `calc(50% - ${RADIUS_DESKTOP + ICON_SIZE_DESKTOP / 2}px)`,
            top: `calc(50% - ${RADIUS_DESKTOP + ICON_SIZE_DESKTOP / 2}px)`,
          }}
        >
          {desktopPositions.map((pos, i) => {
            const center = RADIUS_DESKTOP + ICON_SIZE_DESKTOP / 2;
            return (
              <line
                key={`line-desktop-${i}`}
                x1={center}
                y1={center}
                x2={center + pos.x}
                y2={center + pos.y}
                stroke="currentColor"
                strokeWidth={1}
                className="opacity-50 text-border"
              />
            );
          })}
          {desktopPositions.map((pos, i) => {
            const next = desktopPositions[(i + 1) % total];
            const center = RADIUS_DESKTOP + ICON_SIZE_DESKTOP / 2;
            return (
              <line
                key={`circle-line-desktop-${i}`}
                x1={center + pos.x}
                y1={center + pos.y}
                x2={center + next.x}
                y2={center + next.y}
                stroke="currentColor"
                strokeWidth={1}
                className="opacity-50 text-border"
              />
            );
          })}
        </svg>

        {/* Tech Stack Icons - Desktop */}
        {TECH_STACKS.map((stack, index) => {
          const pos = desktopPositions[index];
          return (
            <div
              key={`desktop-${stack.name}`}
              className="absolute z-10 hidden md:flex"
              style={{
                left: `calc(50% + ${pos.x}px - ${ICON_SIZE_DESKTOP / 2}px)`,
                top: `calc(50% + ${pos.y}px - ${ICON_SIZE_DESKTOP / 2}px)`,
              }}
            >
              <div
                className="rounded-full bg-white flex items-center justify-center shadow-[0_8px_30px_rgb(0,0,0,0.3)]"
                style={{ width: ICON_SIZE_DESKTOP, height: ICON_SIZE_DESKTOP }}
              >
                <Image
                  src={stack.icon}
                  alt={stack.name}
                  width={36}
                  height={36}
                  className="object-contain"
                />
              </div>
            </div>
          );
        })}

        {/* Center Content */}
        <div className="relative z-20 px-4 text-center">
          <h2 className="text-4xl font-bold md:text-6xl text-primary">TECH STACKS</h2>
          <p className="mt-4 text-base md:text-lg w-64 md:w-128 mx-auto">
            Projek-projek dibangun menggunakan stacks dan teknologi yang stable
            dan terbaru
          </p>
        </div>
      </div>
    </section>
  );
}
