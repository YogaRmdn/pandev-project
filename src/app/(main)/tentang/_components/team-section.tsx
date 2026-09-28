import Image from "next/image";

const TEAM_MEMBERS = [
  {
    name: "Ilham",
    role: "Developer",
    image: "/assets/profiles/ilham.png",
  },
  {
    name: "Yoga",
    role: "Developer",
    image: "/assets/profiles/yoga.png",
  },
  {
    name: "Farjihan",
    role: "Developer",
    image: "/assets/profiles/farjihan.png",
  },
  {
    name: "Tahta",
    role: "Developer",
    image: "/assets/profiles/tahta.png",
  },
  {
    name: "Eagel",
    role: "Developer",
    image: "/assets/profiles/eagel.png",
  },
  {
    name: "Masyitah",
    role: "Designer",
    image: "/assets/profiles/masyitah.png",
  },
];

export default function TeamSection() {
  return (
    <section className="py-16 max-w-4xl mx-auto mt-16">
      <div className="text-center mb-10">
        <h2 className="text-3xl font-bold sm:text-4xl uppercase text-primary">Meet Our Team</h2>
        <p className="mt-2 text-muted-foreground">
          Tim profesional yang berpengalaman di bidangnya
        </p>
      </div>
      <div className="grid grid-cols-2 md:flex flex-wrap px-4 justify-center gap-6">
        {TEAM_MEMBERS.map((member) => (
          <div key={member.name} className="flex flex-col items-center text-center">
            <div className="relative mb-4">
              <div className="absolute inset-0 rounded-full bg-primary" />
              <Image
                src={member.image}
                alt={member.name}
                width={"160"}
                height={160}
                className="relative rounded-full object-cover w-auto h-auto"
              />
            </div>
            <h3 className="font-semibold">{member.name}</h3>
            <p className="text-sm text-muted-foreground">{member.role}</p>
          </div>
        ))}
      </div>
    </section>
  );
}
